<?php

/**
 * @Entity(repositoryClass="TokenRepository") @Table(name="token")
 **/
class Token {
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $token;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $refreshToken;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $expiryTime;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set refreshToken
     *
     * @param string $refreshToken
     *
     * @return Token
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Get refreshToken
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set expiryTime
     *
     * @param integer $expiryTime
     *
     * @return Token
     */
    public function setExpiryTime($expiryTime)
    {
        $this->expiryTime = $expiryTime;

        return $this;
    }

    /**
     * Get expiryTime
     *
     * @return integer
     */
    public function getExpiryTime()
    {
        return $this->expiryTime;
    }
}
