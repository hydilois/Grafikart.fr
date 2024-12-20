<?php

namespace App\Domain\Contact;

use Doctrine\ORM\Mapping as ORM;
use geertw\IpAnonymizer\IpAnonymizer;

/**
 * Sauvegarde les demandes de contact afin de limiter le spam.
 */
#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
class ContactRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $ip = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ContactRequest
    {
        $this->id = $id;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): ContactRequest
    {
        $this->ip = $ip;

        return $this;
    }

    public function setRawIp(?string $ip): ContactRequest
    {
        $this->ip = (new IpAnonymizer())->anonymize($ip);

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): ContactRequest
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
