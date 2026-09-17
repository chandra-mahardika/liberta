<?php

namespace Liberta\OAuth;

use Liberta\SqlBuilder\DB;

class ClientRepository
{
    private DB $db;

    public function __construct(?DB $db = null)
    {
        $this->db = $db ?? DB::getInstance();
    }

    public function find(string $clientId): ?array
    {
        return $this->db->table('oauth_clients')
            ->where('client_id', '=', $clientId)
            ->first();
    }

    public function create(array $data): bool
    {
        return $this->db->table('oauth_clients')->insert($data);
    }

    public function validateSecret(string $clientId, string $secret): bool
    {
        $client = $this->find($clientId);
        return $client !== null && password_verify($secret, $client['client_secret']);
    }

    public function getAll(): array
    {
        return $this->db->table('oauth_clients')->get();
    }

    public function delete(string $clientId): bool
    {
        return $this->db->table('oauth_clients')
            ->where('client_id', '=', $clientId)
            ->delete();
    }
}
