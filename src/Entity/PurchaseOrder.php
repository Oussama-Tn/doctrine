<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseOrderRepository")
 */
class PurchaseOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("users:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="purchaseOrders")
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     * @Groups("users:read")
     */
    private $date;

//    /**
//     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="purchaseOrders")
//     */
//    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseOrderProduct", mappedBy="purchaseOrder")
     */
    private $purchaseOrderProducts;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->purchaseOrderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

//    /**
//     * @return Collection|Product[]
//     */
//    public function getProduct(): Collection
//    {
//        return $this->product;
//    }
//
//    public function addProduct(Product $product): self
//    {
//        if (!$this->product->contains($product)) {
//            $this->product[] = $product;
//        }
//
//        return $this;
//    }
//
//    public function removeProduct(Product $product): self
//    {
//        if ($this->product->contains($product)) {
//            $this->product->removeElement($product);
//        }
//
//        return $this;
//    }

    /**
     * @return Collection|PurchaseOrderProduct[]
     */
    public function getPurchaseOrderProducts(): Collection
    {
        return $this->purchaseOrderProducts;
    }

    public function addPurchaseOrderProduct(PurchaseOrderProduct $purchaseOrderProduct): self
    {
        if (!$this->purchaseOrderProducts->contains($purchaseOrderProduct)) {
            $this->purchaseOrderProducts[] = $purchaseOrderProduct;
            $purchaseOrderProduct->setPurchaseOrder($this);
        }

        return $this;
    }

    public function removePurchaseOrderProduct(PurchaseOrderProduct $purchaseOrderProduct): self
    {
        if ($this->purchaseOrderProducts->contains($purchaseOrderProduct)) {
            $this->purchaseOrderProducts->removeElement($purchaseOrderProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseOrderProduct->getPurchaseOrder() === $this) {
                $purchaseOrderProduct->setPurchaseOrder(null);
            }
        }

        return $this;
    }
}
