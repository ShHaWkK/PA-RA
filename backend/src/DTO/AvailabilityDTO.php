<?php
namespace DTO;

class AvailabilityDTO
{
    public $id;
    public $dayOfWeek;
    public $startTime;
    public $endTime;
    public $createdAt;
    public $updatedAt;

    public function __construct($id, $dayOfWeek, $startTime, $endTime, $createdAt, $updatedAt)
    {
        $this->id = $id;
        $this->dayOfWeek = $dayOfWeek;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}
?>
