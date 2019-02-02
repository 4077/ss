<?php namespace ss\controllers;

class MysqlDump extends \Controller
{
    public function run()
    {
        $user = app()->getConfig('databases/default/user');
        $pass = app()->getConfig('databases/default/pass');
        $name = app()->getConfig('databases/default/name');

        $dir = $this->_protected('dump');

        mdir($dir);

        $filePath = $dir . '/' . $name . '.sql';

        $dump = shell_exec('mysqldump -u ' . $user . ' -p' . $pass . ' ' . $name);

        $encryptedDump = encrypt($dump, 'ST%shdtrDDSaaswq');

        write($filePath, $encryptedDump);

        return $filePath;
    }
}
