<?php

namespace V17Development\FlarumUserBadges\Badge\Command;

use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use V17Development\FlarumUserBadges\Badge\Badge;
use V17Development\FlarumUserBadges\Badge\BadgeValidator;
use Illuminate\Support\Arr;

class UpdateBadgeHandler
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var BadgeValidator
     */
    protected $validator;

    /**
     * @param TranslatorInterface $translator
     * @param SettingsRepositoryInterface $settings
     */
    public function __construct(
        TranslatorInterface $translator, 
        SettingsRepositoryInterface $settings,
        BadgeValidator $validator
    ) {
        $this->translator = $translator;
        $this->settings = $settings;
        $this->validator = $validator;
    }

    /**
     * @param UpdateBadge $command
     */
    public function handle(UpdateBadge $command)
    {
        $command->actor->assertAdmin();

        $badge = Badge::findOrFail($command->id);

        // Update name
        if(Arr::has($command->data, "attributes.name")) {
            $badge->name = Arr::get($command->data, "attributes.name", null);
        }

        // Update icon
        if(Arr::has($command->data, "attributes.icon")) {
            $badge->icon = Arr::get($command->data, "attributes.icon", null);
        }

        // Update order
        if(Arr::has($command->data, "attributes.order")) {
            $badge->order = Arr::get($command->data, "attributes.order", null);
        }

        // Update description
        if(Arr::has($command->data, "attributes.description")) {
            $badge->description = Arr::get($command->data, "attributes.description", null);
        }

        // Update visibility
        if(Arr::has($command->data, "attributes.isVisible")) {
            $badge->is_visible = Arr::get($command->data, "attributes.isVisible", null);
        }

        // Validate
        $this->validator->assertValid($badge->getDirty());
        
        // Save
        $badge->save();

        return $badge;
    }
}
