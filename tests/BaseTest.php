<?php

    abstract class BaseTest extends PHPUnit_Framework_TestCase
    {
        /** @var \Cjhbtn\Periscopr\Client $client */
        protected $client;

        /** @var boolean $requiresAuth */
        protected $requiresAuth = true;

        /** @var string $user_id */
        protected $user_id;

        /**
         * PHPUnit Setup Function
         *
         */
        public function setUp()
        {
            if (file_exists(__DIR__ . '/../.env')) {
                Dotenv::load(__DIR__ . '/../');
            }
            $this->client = new \Cjhbtn\Periscopr\Client();
            if ($this->requiresAuth && !$this->setUpAuth()) {
                $this->markTestSkipped("Authenticated test skipped - Unable to authenticate with Periscope API");
            }
        }

        /**
         * Set up authentication for authenticated requests
         *
         * @return boolean
         */
        protected function setUpAuth() {
            $loginRequest = new \Cjhbtn\Periscopr\Requests\LoginTwitter(
                getenv('OAUTH_TOKEN'),
                getenv('OAUTH_TOKEN_SECRET'),
                getenv('TWITTER_USERNAME')
            );
            $loginResponse = $this->client->execute($loginRequest);
            if ($loginResponse->getStatusCode() == 200)
            {
                $this->client->setCookie($loginResponse->cookie);
                $this->user_id = $loginResponse->user->id;
                return true;
            }
            return false;
        }
    }