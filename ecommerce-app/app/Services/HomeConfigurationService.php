<?php

namespace App\Services;

use App\Repositories\Contracts\HomeSectionRepositoryInterface;

class HomeConfigurationService
{
    public function __construct(
        private HomeSectionRepositoryInterface $repository,
        private HomeSectionRendererService $rendererService
    ) {}

    /**
     * Get complete home page configuration with rendered data.
     *
     * @return array
     */
    public function getCompleteConfiguration(): array
    {
        $sections = $this->repository->getAllActive();

        return $sections->map(function ($section) {
            return [
                'uuid' => $section->uuid,
                'type' => $section->type,
                'title' => $section->title,
                'display_order' => $section->display_order,
                'configuration' => $section->configuration,
                'rendered_data' => $this->rendererService->render($section),
            ];
        })->toArray();
    }

    /**
     * Toggle section active status.
     *
     * @param int $sectionId
     * @param bool $isActive
     * @return void
     */
    public function toggleSectionStatus(int $sectionId, bool $isActive): void
    {
        $this->repository->update($sectionId, ['is_active' => $isActive]);
    }

    /**
     * Reorder sections.
     *
     * @param array $sectionIds
     * @return void
     */
    public function reorderSections(array $sectionIds): void
    {
        $this->repository->reorder($sectionIds);
    }
}
