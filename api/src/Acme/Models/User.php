<?php
namespace Acme\Models;

class User extends Base
{
    public function create($data)
    {
        return $this->ci->db->insert("account", $data);
    }
    public function records()
    {
        $categories = $this->ci->db->select("subjects", "*");
        return $categories;
    }

    public function getByEmail($email)
    {
        $this->ci->db->select("account", "username", [
            "email" => $email
        ]);
    }
}
