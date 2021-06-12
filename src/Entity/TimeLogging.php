<?php

namespace App\Entity;

use App\Repository\TimeLoggingRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass=TimeLoggingRepository::class)
 */
class TimeLogging
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enddate = null;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $projectid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statecode = 1;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $userid;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStartdate(): ?DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(DateTimeInterface $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEnddate(): ?DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(?DateTimeInterface $enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getProjectId(): ?string
    {
        return $this->projectid;
    }

    public function setProjectId(?string $projectid): self
    {
        $this->projectid = $projectid;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatecode(): int
    {
        return $this->statecode;
    }

    /**
     * @param int $statecode
     * @return TimeLogging
     */
    public function setStatecode(int $statecode): TimeLogging
    {
        $this->statecode = $statecode;
        return $this;
    }

    /**
     * @param DateTime $startdate
     * @param $projectId
     * @return TimeLogging
     */
    public static function createByStartdate(DateTime $startdate, $userId, $projectId)
    {
        $logdate = new self();
        $logdate->setId(Uuid::uuid4())
            ->setStartdate($startdate)
            ->setUserid($userId)
            ->setProjectId($projectId);
        return $logdate;
    }

    public function getUserid(): ?string
    {
        return $this->userid;
    }

    public function setUserid(string $userid): self
    {
        $this->userid = $userid;

        return $this;
    }
}
