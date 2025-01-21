<?php

namespace App\Models;

use App\Enums\Order\PaymentMethod;
use Carbon\Carbon;
use App\Enums\WaterType;
use App\Enums\Order\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory /*, SoftDeletes, Notifiable*/;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory /*, SoftDeletes, Notifiable*/;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'cause',
        'date',
        'water_type',
        'type',
        'is_now',
        'payment_method',
        'lat',
        'lng',
        'address_id',
        'service_id',
        'client_id',
        'provider_id',
        'coupon_id',
        'location',
        'provider_type',
        'status',
        'service_price',
        'extra_price',
        'provider_profit',
        'provider_percent',
        'coupon_discount',
        'total_price',
        'vat_price',
        'app_percent',
        'app_profit',
        'vat_percent',
        'client_rate',
        'driver_rate',
        'canceller_id',
        'client_completed',
        'provider_completed',
        'cancel_reason',
        'reject_reason',
        'notes',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => Status::class,
        'water_type' => WaterType::class,
        'payment_method' => PaymentMethod::class,
        'date' => 'datetime',
        'is_now' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        // 'relation',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        // 'attribute' => 'default_value',
    ];

    /**
     * Get the route key name for Laravel's route model binding.
     *
     * @return string
     */
    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }

    /********** Relationships **********/

    /**
     * Example relationship: A model has many related models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function orderProviders() {
        return $this->hasMany(OrderProvider::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id');
    }

    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function address() {
        return $this->belongsTo(Address::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function canceller() {
        return $this->belongsTo(User::class, 'canceller_id');
    }

    /********** Accessors & Mutators **********/

    public function getFromArAttribute()
    {
        return $this?->from ? Carbon::createFromFormat('H:i:s', $this?->from)?->translatedFormat('h:i A') : null;
    }

    public function getToArAttribute()
    {
        return $this?->to ? Carbon::createFromFormat('H:i:s', $this?->to)?->translatedFormat('h:i A') : null;
    }

    public function getDistanceAttribute() {
        return haversineGreatCircleDistance($this?->lat, $this?->lng, $this?->provider?->lat, $this?->provider?->lng);
    }
    // get api medias
    public function getApiMediasAttribute()
    {
        return $this?->medias ? $this?->medias?->map(function ($media) {
            return asset('storage/' . $media->path);
        }) : [];
    }


    // scopes
    public function scopeStatusIn($query, $status)
    {
        return $query->whereIn('status', Status::mapStatus($status));
    }
}
