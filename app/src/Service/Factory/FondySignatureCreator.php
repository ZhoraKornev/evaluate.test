<?php


namespace App\Service\Factory;


class FondySignatureCreator
{
    private string $password;
    private string $merchant;
    /**
     * FondySignatureCreator constructor.
     *
     * @param string $password
     * @param string $merchant
     */
    public function __construct(
        string $password,
        string $merchant
    ) {}


    /**
     * Generate request params signature
     *
     * @param array $params
     *
     * @return string
     */
    public function generate(array $params) {
        $params['merchant_id'] = $this->merchant;
        $params = array_filter($params,'strlen');
        ksort($params);
        $params = array_values($params);
        array_unshift( $params , $this->password );
        $params = join('|',$params);
        return(sha1($params));
    }

    /**
     * Sign params with signature
     *
     * @param array $params
     *
     * @return array
     */
    public function sign(array $params) {
        if (array_key_exists('signature', $params)) {
            return $params;
        }
        $params['signature'] = self::generate($params);

        return $params;
    }


}
