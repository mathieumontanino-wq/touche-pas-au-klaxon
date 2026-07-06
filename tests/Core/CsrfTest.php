<?php

declare(strict_types=1);

namespace Tests\Core;

use App\Core\Csrf;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires de la protection CSRF.
 */
final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function testGenerateTokenProduitUnJetonNonVide(): void
    {
        $token = Csrf::generateToken();

        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token));
    }

    public function testGenerateTokenReutiliseLeMemeJetonSurPlusieursAppels(): void
    {
        $premier = Csrf::generateToken();
        $second = Csrf::generateToken();

        $this->assertSame($premier, $second);
    }

    public function testIsValidRetourneVraiPourLeBonJeton(): void
    {
        $token = Csrf::generateToken();

        $this->assertTrue(Csrf::isValid($token));
    }

    public function testIsValidRetourneFauxPourUnJetonIncorrect(): void
    {
        Csrf::generateToken();

        $this->assertFalse(Csrf::isValid('jeton-invalide'));
    }

    public function testIsValidRetourneFauxPourUnJetonNull(): void
    {
        Csrf::generateToken();

        $this->assertFalse(Csrf::isValid(null));
    }
}