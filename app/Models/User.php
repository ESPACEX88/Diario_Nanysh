<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all diary entries for the user.
     */
    public function diaryEntries()
    {
        return $this->hasMany(DiaryEntry::class);
    }

    /**
     * Get all notes for the user.
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get all photos for the user.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Get all albums for the user.
     */
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Get all goals for the user.
     */
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Get all habits for the user.
     */
    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    /**
     * Get all habit logs for the user.
     */
    public function habitLogs()
    {
        return $this->hasMany(HabitLog::class);
    }

    /**
     * Get all gratitude entries for the user.
     */
    public function gratitudes()
    {
        return $this->hasMany(Gratitude::class);
    }

    /**
     * Get all tags for the user.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get the user's settings.
     */
    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the user's pet.
     */
    public function pet()
    {
        return $this->hasOne(Pet::class);
    }

    /**
     * Get todos for the user.
     */
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * Get events for the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get dreams for the user.
     */
    public function dreams()
    {
        return $this->hasMany(Dream::class);
    }

    /**
     * Get wishlist items for the user.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }
}
