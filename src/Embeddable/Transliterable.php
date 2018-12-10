<?php

namespace Tomchkk\TransliterableBundle\Embeddable;

/**
 * Transliterable
 *
 * An embeddable entity that enables an entity text field to simply be mapped
 * as a transliterable.
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class Transliterable
{
    /**
     * @var string
     */
    private $original;

    /**
     * @var string
     */
    private $transliteration;


    /**
     * Get the value of original
     *
     * @return string
     */
    public function getOriginal(): ?string
    {
        return $this->original;
    }

    /**
     * Set the value of original
     *
     * Setting a new original value also clears any transliteration.
     *
     * @param string $original
     *
     * @return self
     */
    public function setOriginal(string $original): self
    {
        if ($original !== $this->original) {
            $this->original = $original;
            $this->transliteration = null;
        }

        return $this;
    }

    /**
     * Get the value of transliteration
     *
     * @return string
     */
    public function getTransliteration(): ?string
    {
        return $this->transliteration;
    }

    /**
     * Set the value of transliteration
     *
     * @param string $transliteration
     *
     * @return self
     */
    public function setTransliteration(string $transliteration): self
    {
        $this->transliteration = $transliteration;

        return $this;
    }
}
