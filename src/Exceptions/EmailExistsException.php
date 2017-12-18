<?php namespace Qualia\Exceptions;

class EmailExistsException extends RequestException
{
    /**
     * @var array
     */
    private $entryId;

    public function __construct($entryId)
    {
        $this->entryId = $entryId;
        $this->message = "Entry with such email already exists, entry id: $entryId";
    }

    /**
     * Returns ID of the entry that has email you already submitted
     *
     * @return array
     */
    public function getEntryId()
    {
        return $this->entryId;
    }
}