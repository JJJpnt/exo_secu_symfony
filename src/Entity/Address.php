<?php

namespace App\Entity;

//use App\Repository\AddressEmbeddableRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

use Symfony\Component\Validator\Constraints as Assert;

// class Address implements JsonSerializable
class Address
{
    /**
     * Numéro voie
     *
     * @var string
     * @Assert\Length(max=10, maxMessage="Le numéro de voie doit comporter {{ max }} caractères maximum.")
     */
    private $num_voie;

    /**
     * Type voie
     *
     * @var string
     * @Assert\Length(max=10, maxMessage="Le type de voie doit comporter {{ max }} caractères maximum.")
     */
    private $type_voie;

    /**
     * Ligne 1
     *
     * @var string
     * @Assert\Length(max=40, maxMessage="La ligne 1 doit comporter {{ max }} caractères maximum.")
     */
    private $ligne_1;

    /**
     * Ligne 2
     *
     * @var string
     * @Assert\Length(max=40, maxMessage="La ligne 2 doit comporter {{ max }} caractères maximum.")
     */
    private $ligne_2;

    /**
     * Code Postal
     *
     * @var string
     * @Assert\Length(max=5, maxMessage="Le code postal doit comporter {{ max }} caractères maximum.")
     */
    private $code_postal;

    /**
     * Commune
     *
     * @var string
     * @Assert\Length(max=40, maxMessage="La commune doit comporter {{ max }} caractères maximum.")
     */
    private $commune;

    /**
     * @var array
     */
    private $coordinates;

    public function getNumVoie(): ?string
    {
        return $this->num_voie;
    }

    public function setNumVoie(?string $num_voie): self
    {
        $this->num_voie = $num_voie;

        return $this;
    }

    public function getTypeVoie(): ?string
    {
        return $this->type_voie;
    }

    public function setTypeVoie(?string $type_voie): self
    {
        $this->type_voie = $type_voie;

        return $this;
    }

    public function getLigne1(): ?string
    {
        return $this->ligne_1;
    }

    public function setLigne1(string $ligne_1): self
    {
        $this->ligne_1 = $ligne_1;

        return $this;
    }

    public function getLigne2(): ?string
    {
        return $this->ligne_2;
    }

    public function setLigne2(?string $ligne_2): self
    {
        $this->ligne_2 = $ligne_2;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getCoordinates(): ?string
    {
        return $this->num_voie;
    }

    public function setCoordinates(?string $num_voie): self
    {
        $this->num_voie = $num_voie;

        return $this;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'num_voie' => $this->num_voie,
            'type_voie' => $this->type_voie,
            'ligne_1' => $this->ligne_1,
            'ligne_2' => $this->ligne_2,
            'code_postal' => $this->code_postal,
            'commune' => $this->commune,
            'coordinates' => $this->coordinates,
        ]);
    }
    public function toArray()
    {
        return [
            'num_voie' => $this->num_voie,
            'type_voie' => $this->type_voie,
            'ligne_1' => $this->ligne_1,
            'ligne_2' => $this->ligne_2,
            'code_postal' => $this->code_postal,
            'commune' => $this->commune,
            'coordinates' => $this->coordinates,
        ];
    }
}
