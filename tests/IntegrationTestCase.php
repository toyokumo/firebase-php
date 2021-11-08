<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests;

use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Util;
use Kreait\Firebase\Util\JSON;
use Throwable;

abstract class IntegrationTestCase extends FirebaseTestCase
{
    public const TENANT_ID = 'Beste-lgiu8';

    protected static Factory $factory;

    protected static ServiceAccount $serviceAccount;

    /** @var string[] */
    protected static array $registrationTokens = [];

    protected static string $unknownToken = 'd_RTtLHR_JgI4r4tbYM9CA:APA91bEzb2Tb3WlKwddpEPYY2ZAx7AOmjOhiw-jVq6J9ekJGpBAefAgMb1muDJcKBMsrMq7zSCfBzl0Ll7JCZ0o8QI9zLVG1F18nqW9AOFKDXi8-MyT3R5Stt6GGKnq9rd9l5kopGEbO';

    public static function setUpBeforeClass(): void
    {
        $credentials = self::credentialsFromEnvironment() ?? self::credentialsFromFile();

        if (!$credentials instanceof \Kreait\Firebase\ServiceAccount) {
            self::markTestSkipped('The integration tests require credentials');
        }

        self::$serviceAccount = $credentials;

        self::$factory = (new Factory())->withServiceAccount(self::$serviceAccount);

        self::$registrationTokens = self::registrationTokensFromEnvironment() ?? self::registrationTokensFromFile() ?? [];
    }

    protected function createUserWithEmailAndPassword(?string $email = null, ?string $password = null): UserRecord
    {
        $email ??= self::randomEmail();
        $password ??= self::randomString();

        return self::$factory
            ->createAuth()
            ->createUser([
                'email' => $email,
                'clear_text_password' => $password,
            ])
        ;
    }

    protected function deleteUser(UserRecord|\Stringable|string $userOrUid): void
    {
        $uid = $userOrUid instanceof UserRecord ? $userOrUid->uid : $userOrUid;

        try {
            self::$factory->createAuth()->deleteUser($uid);
        } catch (Throwable) {
            // Well, if that failed, *we're* failed
        }
    }

    protected function getTestRegistrationToken(): string
    {
        if (empty(self::$registrationTokens)) {
            $this->markTestSkipped('No registration token available');
        }

        return self::$registrationTokens[\random_int(0, \count(self::$registrationTokens) - 1)];
    }

    protected static function randomString(string $suffix = ''): string
    {
        return \mb_strtolower(\bin2hex(\random_bytes(5)).$suffix);
    }

    protected static function randomEmail(string $suffix = ''): string
    {
        return self::randomString($suffix.'@domain.tld');
    }

    private static function credentialsFromFile(): ?ServiceAccount
    {
        $credentialsPath = self::$fixturesDir.'/test_credentials.json';

        if (!\file_exists($credentialsPath)) {
            return null;
        }

        try {
            return ServiceAccount::fromValue($credentialsPath);
        } catch (Throwable) {
            return null;
        }
    }

    private static function credentialsFromEnvironment(): ?ServiceAccount
    {
        if ($credentials = Util::getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            return ServiceAccount::fromValue($credentials);
        }

        return null;
    }

    /**
     * @return string[]|null
     */
    private static function registrationTokensFromFile(): ?array
    {
        $path = self::$fixturesDir.'/test_devices.json';

        if (!\file_exists($path)) {
            return null;
        }

        try {
            if ($contents = \file_get_contents($path)) {
                return JSON::decode($contents, true);
            }

            return null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return string[]|null
     */
    private static function registrationTokensFromEnvironment(): ?array
    {
        if (!($tokens = Util::getenv('TEST_REGISTRATION_TOKENS'))) {
            return null;
        }

        try {
            return JSON::decode($tokens, true);
        } catch (Throwable) {
            return null;
        }
    }
}
