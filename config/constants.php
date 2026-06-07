<?php
date_default_timezone_set('Asia/Dhaka');

// =========================
// APP CONFIG
// =========================
define('APP_NAME', 'PureCinepix');
define('BASE_URL', '/purecinepix/');


// =========================
// USER ROLES
// =========================
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPERADMIN', 'superadmin');


// =========================
// CONTENT TYPES
// =========================
define('TYPE_MOVIE', 'movie');
define('TYPE_TV', 'tv');


// =========================
// CATEGORY LIMITS (OPTIONAL BACKUP)
// (DB already controls this, but keep as fallback)
// =========================
define('LIMIT_FEATURED', 10);
define('LIMIT_TRENDING', 20);
define('LIMIT_DEFAULT', 50);


// =========================
// SPECIAL CATEGORY IDs
// (You will fetch these once and hardcode)
// =========================
define('CAT_FEATURED', 1);
define('CAT_TRENDING', 2);


// =========================
// TMDB CONFIG
// =========================
define('TMDB_API_KEY', '');
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p/w500');


// =========================
// SESSION KEYS
// =========================
define('SESSION_USER', 'user');


// =========================
// PAGINATION (future use)
// =========================
define('ITEMS_PER_PAGE', 20);