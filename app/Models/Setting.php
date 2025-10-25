<?php

namespace App\Models;
// app/Models/Setting.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
        'site_tagline',
        'logo_path',
        'hero_text',
        'about_us',
        'vision',
        'about_image_path',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_website',
        'work_hours',
        'social_facebook',
        'social_youtube',
        'social_instagram',
        'social_twitter',
        'footer_description',
    ];
}