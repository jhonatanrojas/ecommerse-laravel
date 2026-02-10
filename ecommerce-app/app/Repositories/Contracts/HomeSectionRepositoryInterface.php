<?php

namespace App\Repositories\Contracts;

use App\Models\HomeSection;
use Illuminate\Support\Collection;

interface HomeSectionRepositoryInterface
{
    /**
     * Get all active home sections ordered by display order.
     *
     * @return Collection
     */
    public function getAllActive(): Collection;

    /**
     * Get a home section by ID with items.
     *
     * @param int $id
     * @return HomeSection
     */
    public function getById(int $id): HomeSection;

    /**
     * Create a new home section.
     *
     * @param array $data
     * @return HomeSection
     */
    public function create(array $data): HomeSection;

    /**
     * Update a home section.
     *
     * @param int $id
     * @param array $data
     * @return HomeSection
     */
    public function update(int $id, array $data): HomeSection;

    /**
     * Soft delete a home section.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Reorder home sections.
     *
     * @param array $sectionIds
     * @return void
     */
    public function reorder(array $sectionIds): void;
}
