<?php

namespace Koodilab\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Koodilab\Models\Relations\HasManyPlanet;
use Laravel\Passport\HasApiTokens;

/**
 * User.
 *
 * @property int $id
 * @property int|null $capital_id
 * @property int|null $current_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property bool $is_enabled
 * @property int $role
 * @property int $energy
 * @property int $experience
 * @property int $production_rate
 * @property \Carbon\Carbon|null $last_login
 * @property \Carbon\Carbon|null $last_capital_changed
 * @property \Carbon\Carbon|null $last_production_changed
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|Planet[] $planets
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCapitalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEnergy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastCapitalChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastProductionChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProductionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasManyPlanet;

    /**
     * The user role.
     *
     * @var int
     */
    const ROLE_USER = 0;

    /**
     * The administrator role.
     *
     * @var int
     */
    const ROLE_ADMIN = 1;

    /**
     * The super admin role.
     *
     * @var int
     */
    const ROLE_SUPER_ADMIN = 2;

    /**
     * {@inheritdoc}
     */
    protected $attributes = [
        'is_enabled' => true,
        'role' => self::ROLE_USER,
        'energy' => 1000,
        'experience' => 0,
        'production_rate' => 0,
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [
        'id', 'remember_token', 'created_at', 'updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'last_login', 'last_capital_changed', 'last_production_changed', 'started_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'is_enabled' => 'bool',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            return auth()->id() != $model->getKey();
        });
    }

    /**
     * Set the password attribute.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Is admin?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role == static::ROLE_ADMIN;
    }

    /**
     * Is super admin?
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->role == static::ROLE_SUPER_ADMIN;
    }

    /**
     * Can give this role?
     *
     * @param string $role
     *
     * @return bool
     */
    public function canGiveRole($role)
    {
        return $this->role >= $role;
    }
}