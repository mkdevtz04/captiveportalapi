<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class MikrotikService
{
    protected $ip;
    protected $user;
    protected $pass;
    protected $port;
    protected $socket;

    public function __construct(array $settings = [])
    {
        $this->ip   = $settings['ip']   ?? config('services.mikrotik.ip');
        $this->user = $settings['user'] ?? config('services.mikrotik.user');
        $this->pass = $settings['password'] ?? config('services.mikrotik.password');
        $this->port = $settings['port'] ?? config('services.mikrotik.port', 8728);
    }

    public function connect(): bool
    {
        try {
            $this->socket = @fsockopen(
                $this->ip,
                $this->port,
                $errno,
                $errstr,
                10
            );

            if (!$this->socket) {
                Log::error("MikroTik connection failed: $errstr ($errno)");
                return false;
            }

            return $this->login();

        } catch (\Exception $e) {
            Log::error('MikroTik connect error: ' . $e->getMessage());
            return false;
        }
    }

    public function createHotspotUser(
        string $username,
        string $password,
        string $profile
    ): bool {
        try {
            $this->writeWord('/ip/hotspot/user/add');
            $this->writeWord('=name='     . $username);
            $this->writeWord('=password=' . $password);
            $this->writeWord('=profile='  . $profile);
            $this->writeSentenceEnd();

            $response = $this->read();

            Log::info('MikroTik user created', [
                'username' => $username,
                'profile'  => $profile,
                'response' => $response,
            ]);

            return in_array('!done', $response);

        } catch (\Exception $e) {
            Log::error('MikroTik createHotspotUser: ' . $e->getMessage());
            return false;
        }
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }

    public function listHotspotUsers(): array
    {
        try {
            $this->writeWord('/ip/hotspot/user/print');
            $this->writeSentenceEnd();
            $response = $this->read();
            return $this->parseKeyValueResponse($response);
        } catch (\Exception $e) {
            Log::error('MikroTik listHotspotUsers: ' . $e->getMessage());
            return [];
        }
    }

    public function listProfiles(): array
    {
        try {
            $this->writeWord('/ip/hotspot/user/profile/print');
            $this->writeSentenceEnd();
            $response = $this->read();
            return $this->parseKeyValueResponse($response);
        } catch (\Exception $e) {
            Log::error('MikroTik listProfiles: ' . $e->getMessage());
            return [];
        }
    }

    public function removeHotspotUser(string $id): bool
    {
        try {
            $this->writeWord('/ip/hotspot/user/remove');
            $this->writeWord('=numbers=' . $id);
            $this->writeSentenceEnd();
            $response = $this->read();
            Log::info('MikroTik user removed', ['id' => $id, 'response' => $response]);
            return in_array('!done', $response);
        } catch (\Exception $e) {
            Log::error('MikroTik removeHotspotUser: ' . $e->getMessage());
            return false;
        }
    }

    public function disableHotspotUser(string $id): bool
    {
        try {
            $this->writeWord('/ip/hotspot/user/set');
            $this->writeWord('=numbers=' . $id);
            $this->writeWord('=disabled=yes');
            $this->writeSentenceEnd();
            $response = $this->read();
            Log::info('MikroTik user disabled', ['id' => $id, 'response' => $response]);
            return in_array('!done', $response);
        } catch (\Exception $e) {
            Log::error('MikroTik disableHotspotUser: ' . $e->getMessage());
            return false;
        }
    }

    private function login(): bool
    {
        $this->writeWord('/login');
        $this->writeWord('=name='     . $this->user);
        $this->writeWord('=password=' . $this->pass);
        $this->writeSentenceEnd();
        $response = $this->read();
        return in_array('!done', $response);
    }

    private function writeWord(string $word): void
    {
        $len = strlen($word);
        if ($len < 0x80) {
            fwrite($this->socket, chr($len));
        } elseif ($len < 0x4000) {
            $len |= 0x8000;
            fwrite($this->socket, chr(($len >> 8) & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        } else {
            $len |= 0xC00000;
            fwrite($this->socket, chr(($len >> 16) & 0xFF));
            fwrite($this->socket, chr(($len >> 8)  & 0xFF));
            fwrite($this->socket, chr($len & 0xFF));
        }
        fwrite($this->socket, $word);
    }

    private function writeSentenceEnd(): void
    {
        fwrite($this->socket, chr(0));
    }

    private function read(): array
    {
        $response = [];
        while (true) {
            $lenByte = ord(fread($this->socket, 1));
            if ($lenByte === 0) break;

            if ($lenByte & 0x80) {
                $lenByte2 = ord(fread($this->socket, 1));
                $len = (($lenByte & 0x3F) << 8) | $lenByte2;
            } else {
                $len = $lenByte;
            }

            $word = '';
            while (strlen($word) < $len) {
                $word .= fread($this->socket, $len - strlen($word));
            }
            $response[] = $word;
        }
        return $response;
    }

    private function parseKeyValueResponse(array $response): array
    {
        $items = [];
        $current = [];
        foreach ($response as $word) {
            if ($word === '!done') {
                if (!empty($current)) {
                    $items[] = $current;
                }
                continue;
            }
            if (str_starts_with($word, '=')) {
                $parts = explode('=', $word, 3);
                $key = ltrim($parts[1] ?? $parts[0], '=');
                $value = $parts[2] ?? '';
                $current[$key] = $value;
            } elseif ($word === '!re' || $word === '!trap') {
                if (!empty($current)) {
                    $items[] = $current;
                    $current = [];
                }
            }
        }
        if (!empty($current)) {
            $items[] = $current;
        }
        return $items;
    }
}