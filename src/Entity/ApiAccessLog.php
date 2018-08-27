<?php
    namespace App\Entity;
    use Doctrine\ORM\Mapping as ORM;
    use ApiPlatform\Core\Annotation\ApiResource;
    use ApiPlatform\Core\Annotation\ApiFilter;
    use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\ApiAccessLogRepository")
     * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="AccessLog", columns={"patient_id","service","entity"})})
     *
     * @ApiResource
     * @ApiFilter(SearchFilter::class, properties={"id": "exact", "patient_id": "exact", "service": "exact", "entity": "exact"})
     */
    class ApiAccessLog
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\Patient")
         * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
         */
        private $patient;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\ThirdPartyService")
         * @ORM\JoinColumn(name="service", referencedColumnName="id")
         */
        private $thirdPartyService;

        /**
         * @ORM\Column(type="string", length=30, nullable=true)
         */
        private $entity;

        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $lastRetrieved;

        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $lastPulled;

        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $cooldown;
    
        public function getId(): ?int
        {
            return $this->id;
        }
    
        public function getEntity(): ?string
        {
            return $this->entity;
        }
    
        public function setEntity(?string $entity): self
        {
            $this->entity = $entity;
    
            return $this;
        }
    
        public function getLastRetrieved(): ?\DateTimeInterface
        {
            return $this->lastRetrieved;
        }
    
        public function setLastRetrieved(?\DateTimeInterface $lastRetrieved): self
        {
            $this->lastRetrieved = $lastRetrieved;
    
            return $this;
        }
    
        public function getPatient(): ?Patient
        {
            return $this->patient;
        }
    
        public function setPatient(?Patient $patient): self
        {
            $this->patient = $patient;
    
            return $this;
        }
    
        public function getThirdPartyService(): ?ThirdPartyService
        {
            return $this->thirdPartyService;
        }
    
        public function setThirdPartyService(?ThirdPartyService $thirdPartyService): self
        {
            $this->thirdPartyService = $thirdPartyService;
    
            return $this;
        }
    
        public function getLastPulled(): ?\DateTimeInterface
        {
            return $this->lastPulled;
        }
    
        public function setLastPulled(?\DateTimeInterface $lastPulled): self
        {
            $this->lastPulled = $lastPulled;
    
            return $this;
        }
    
        public function getCooldown(): ?\DateTimeInterface
        {
            return $this->cooldown;
        }
    
        public function setCooldown(?\DateTimeInterface $cooldown): self
        {
            $this->cooldown = $cooldown;
    
            return $this;
        }

    }