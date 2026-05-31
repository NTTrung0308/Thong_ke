<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TrainingResult extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    // Tên bảng
    protected $fillable = [
        'unit_id', 'unit_name_at_time',
        'training_date', 'training_month', 'training_subject_id', 'content',
        'start_time', 'end_time', 'duration_hours',
        'trung_doi_count', 'at_count', 'kdt_count',
        'result', 'result_details', 'passing_rate',
        'evaluation', 'strengths', 'weaknesses', 'recommendations',
        'instructor', 'supervisor',
        'attachment', 'created_by', 'updated_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Định dạng ngày giờ và kiểu dữ liệu
    protected $casts = [
        'training_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'passing_rate' => 'decimal:2',
    ];

    // Relationships
    public function trainingSubject()
    {
        return $this->belongsTo(TrainingSubject::class, 'training_subject_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFormattedTrainingDateAttribute()
    {
        return $this->training_date ? $this->training_date->format('d/m/Y') : '';
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : '';
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i') : '';
    }

    public function getResultNameAttribute()
    {
        $results = [
            'xuất_sắc' => 'Xuất sắc',
            'giỏi' => 'Giỏi',
            'khá' => 'Khá',
            'trung_bình' => 'Trung bình',
            'yếu' => 'Yếu',
        ];

        return $results[$this->result] ?? 'Chưa đánh giá';
    }

    public function getResultBadgeAttribute()
    {
        $badges = [
            'xuất_sắc' => 'badge-success',
            'giỏi' => 'badge-primary',
            'khá' => 'badge-info',
            'trung_bình' => 'badge-warning',
            'yếu' => 'badge-danger',
        ];

        return $badges[$this->result] ?? 'badge-secondary';
    }

    public function getTotalParticipantsAttribute()
    {
        return $this->trung_doi_count + $this->at_count + $this->kdt_count;
    }

    // Scopes
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('result', $result);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('training_date', $year);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->whereMonth('training_date', $month);
    }

    // Safe HTML output accessor (sanitized)
    public function getSafeContentAttribute()
    {
        if (! $this->content) {
            return '';
        }

        static $purifier = null;
        if ($purifier === null) {
            $config = \HTMLPurifier_Config::createDefault();
            // Allow basic tags and safe attributes
            $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,a[href|title|target],ul,ol,li,span,div,table,thead,tbody,tr,th,td,img[src|alt|width|height],h1,h2,h3,h4,h5,h6');
            $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www.youtube.com/embed/|player.vimeo.com/video/)%');
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $purifier = new \HTMLPurifier($config);
        }

        return $purifier->purify($this->content);
    }
}
