<?php

namespace V17Development\FlarumUserBadges;

use Flarum\Extend;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\User\User;
use Flarum\Api\Controller as FlarumController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__ . '/less/Forum.less')
    ,
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__ . '/less/Admin.less'),

    (new Extend\Routes('api'))
        // Badges
        ->get('/badges', 'badges.overview', Api\Controller\ListBadgesController::class)
        ->post('/badges', 'badges.create', Api\Controller\CreateBadgeController::class)
        ->post('/badges/order', 'badges.order', Api\Controller\OrderBadgesController::class)
        ->patch('/badges/{id}', 'badges.update', Api\Controller\UpdateBadgeController::class)
        ->delete('/badges/{id}', 'badges.delete', Api\Controller\DeleteBadgeController::class)

        // Badge categories
        ->get('/badge_categories', 'badge.categories.overview', Api\Controller\ListBadgeCategoriesController::class)
        ->post('/badge_categories', 'badge.categories.create', Api\Controller\CreateBadgeCategoryController::class)
        ->post('/badge_categories/order', 'badge.categories.order', Api\Controller\OrderBadgeCategoriesController::class)
        ->patch('/badge_categories/{id}', 'badge.categories.update', Api\Controller\UpdateBadgeCategoryController::class)
        ->delete('/badge_categories/{id}', 'badge.categories.delete', Api\Controller\DeleteBadgeCategoryController::class)

        // User badges
        ->get('/user_badges', 'badge.users.overview', Api\Controller\ListUserBadgesController::class)
        ->post('/user_badges', 'badge.users.create', Api\Controller\CreateUserBadgeController::class)
        ->patch('/user_badges/{id}', 'badge.users.update', Api\Controller\UpdateUserBadgeController::class)
        ->delete('/user_badges/{id}', 'badge.users.delete', Api\Controller\DeleteUserBadgeController::class)
    ,

    // Extension permissions
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('canGiveBadge', function(ForumSerializer $serializer) {
            return $serializer->getActor()->hasPermission("badges.giveBadge");
        }),

    // Badges relation with User
    (new Extend\Model(User::class))
        ->hasMany('userBadges', UserBadge\UserBadge::class, 'user_id')
        ->relationship('userPrimaryBadge', function($user) {
            return $user->hasOne(UserBadge\UserBadge::class, 'user_id', null)->where('is_primary', true);
        }),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->hasMany('userBadges', Api\Serializer\UserBadgeSerializer::class)
        ->hasOne('userPrimaryBadge', Api\Serializer\UserBadgeSerializer::class),

    (new Extend\ApiController(FlarumController\ShowUserController::class))
        ->addInclude(['userBadges', 'userBadges.badge', 'userBadges.badge.category', 'userPrimaryBadge', 'userPrimaryBadge.badge']),

    (new Extend\ApiController(FlarumController\ListUsersController::class))
        ->addInclude(['userBadges', 'userPrimaryBadge', 'userPrimaryBadge.badge']),

    new Extend\Locales(__DIR__ . '/locale'),
];
