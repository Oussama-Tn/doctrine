<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("products:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductCategory", inversedBy="products")
     * @Groups("products:read")
     */
    private $productCategory;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("products:read")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("products:read")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups("products:read")
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("products:read")
     */
    private $isAvailable;

//    /**
//     * @ORM\ManyToMany(targetEntity="App\Entity\PurchaseOrder", mappedBy="product")
//     */
//    private $purchaseOrders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseOrderProduct", mappedBy="product")
     */
    private $purchaseOrderProducts;

    public function __construct()
    {
        //$this->purchaseOrders = new ArrayCollection();
        $this->purchaseOrderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    public function setProductCategory(?ProductCategory $productCategory): self
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

//    /**
//     * @return Collection|PurchaseOrder[]
//     */
//    public function getPurchaseOrders(): Collection
//    {
//        return $this->purchaseOrders;
//    }
//
//    public function addPurchaseOrder(PurchaseOrder $purchaseOrder): self
//    {
//        if (!$this->purchaseOrders->contains($purchaseOrder)) {
//            $this->purchaseOrders[] = $purchaseOrder;
//            $purchaseOrder->addProduct($this);
//        }
//
//        return $this;
//    }
//
//    public function removePurchaseOrder(PurchaseOrder $purchaseOrder): self
//    {
//        if ($this->purchaseOrders->contains($purchaseOrder)) {
//            $this->purchaseOrders->removeElement($purchaseOrder);
//            $purchaseOrder->removeProduct($this);
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
            $purchaseOrderProduct->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseOrderProduct(PurchaseOrderProduct $purchaseOrderProduct): self
    {
        if ($this->purchaseOrderProducts->contains($purchaseOrderProduct)) {
            $this->purchaseOrderProducts->removeElement($purchaseOrderProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseOrderProduct->getProduct() === $this) {
                $purchaseOrderProduct->setProduct(null);
            }
        }

        return $this;
    }
}
