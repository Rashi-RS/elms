<?php
declare(strict_types=1);

namespace App\helpers;

class ViewHelper
{
    public static function e(?string $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    public static function url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return APP_URL . ($path !== '' ? '/' . $path : '');
    }

    public static function asset(string $path = ''): string
    {
        return self::url('assets/' . ltrim($path, '/'));
    }

    public static function csrf(): string
    {
        return Csrf::field();
    }

    public static function csrfMeta(): string
    {
        return Csrf::meta();
    }

    public static function statusBadge(string $status): string
    {
        $statusLower = strtolower($status);
        $class = match ($statusLower) {
            'active', 'approved', 'present' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'rejected', 'inactive', 'terminated', 'absent' => 'bg-danger',
            'half_day', 'half day' => 'bg-info text-dark',
            'leave', 'cancelled' => 'bg-secondary',
            'suspended' => 'bg-dark',
            default => 'bg-light text-dark',
        };

        $label = ucwords(str_replace('_', ' ', $status));
        return '<span class="badge ' . $class . ' px-2.5 py-1.5">' . self::e($label) . '</span>';
    }

    public static function formatDate(?string $date, string $format = 'd M Y'): string
    {
        if (empty($date)) {
            return '—';
        }
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : $date;
    }

    public static function formatTime(?string $time, string $format = 'h:i A'): string
    {
        if (empty($time)) {
            return '—';
        }
        $timestamp = strtotime($time);
        return $timestamp ? date($format, $timestamp) : $time;
    }

    public static function formatCurrency(float|int|string $amount): string
    {
        return '$' . number_format((float)$amount, 2);
    }

    public static function activeNav(string $pattern): string
    {
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($currentUri, PHP_URL_PATH) ?? '/';

        if ($pattern === '/' && $path === '/') {
            return 'active';
        }

        if ($pattern !== '/' && str_starts_with($path, $pattern)) {
            return 'active';
        }

        return '';
    }
}
