<?php
namespace MtHash\Model\User;
use MtHash\Model\AbstractEntity;


class User extends AbstractEntity
{
    public $id, $name, $login, $password, $is_demo, $is_admin, $tag;

    const   DEMO_USER_ID    = 2;

    public $rules    = [
        'name'                  => ['required'],
        'login'                 => ['required', 'email'],
        'password'              => ['required', ['lengthMin', 8]]
    ];

    public function initialize()
    {
        $this->setSource ('user');
    }

    public function beforeSave()
    {
        if ($this->hasSnapshotData() && $this->hasChanged('login'))
        {
            if (self::count (['status > 0 and login = ?0', 'bind' => [$this->login]]) > 0) throw new \ValidationException('User with such email is already registered');
        }
        $this->password = substr ($this->password, 0, 4) == '$2y$' ? $this->password : password_hash ($this->password, PASSWORD_BCRYPT);
    }

    public function beforeCreate()
    {
        parent::beforeCreate();
        if (self::count (['status > 0 and login = ?0', 'bind' => [$this->login]]) > 0) throw new \ValidationException('User with such email is already registered');
    }

    public function getCurrent()
    {
        $token      = $this->getDI()->get('request')->getHeader (Jwt::HEADER);
        if (empty ($token)) throw new \TokenException('Authorization Token is empty');

        $tokenData  = Jwt::fetch ($token);

        return User::failFindFirst($tokenData['id']);
    }

    public function getWallet (?string $symbol = null) : Wallet
    {
        if (empty ($symbol)) $symbol = 'HASH';
        return Wallet::failFindFirst(['status > 0 and currency = ?0 and user_id = ?1', 'bind' => [$symbol, $this->id]]);
    }

    public function createDemo(?string $tag = null) : User
    {
        if (is_null ($tag)) $tag = time();

        $data   = [
            'login' => 'demo-' . $tag . '@mthash.com', 'name' => 'Demo ' . $tag . '-User', 'password' => password_hash (12345678, PASSWORD_BCRYPT), 'is_demo' => 1,
            'tag'   => $tag,
        ];
        return $this->createEntity ($data);
    }


}