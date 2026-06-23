<div class="hero-section animate-fade-up" style="min-height:auto;padding:28px 32px;">
    <div class="anim-wave" style="position:absolute;bottom:0;left:0;right:0;height:30px;z-index:0;opacity:0.3;">
        <svg viewBox="0 0 1440 40" preserveAspectRatio="none" style="width:200%;height:100%;">
            <defs>
                <linearGradient id="waveGrad_<?= $heroId ?? 'default' ?>" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#3b82f6" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="#06b6d4" stop-opacity="0"/>
                </linearGradient>
            </defs>
            <path class="anim-wave-fill" fill="url(#waveGrad_<?= $heroId ?? 'default' ?>)" d="M0,20 C240,0 480,40 720,20 C960,0 1200,40 1440,20 L1440,40 L0,40 Z"/>
        </svg>
    </div>
    <div style="position:relative;z-index:1;">
        <div>
            <h1 class="hero-title" style="font-size:1.5rem;">
                <?php if ($heroIcon ?? ''): ?><i class="<?= $heroIcon ?>" style="color:var(--blue-primary);margin-right:8px;"></i><?php endif; ?>
                <?= $heroTitle ?? 'Page' ?>
            </h1>
            <?php if ($heroSubtitle ?? ''): ?>
            <p class="hero-subtitle" style="font-size:0.85rem;"><?= $heroSubtitle ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($heroStats)): ?>
    <div class="hero-stats" style="position:relative;z-index:1;margin-top:16px;">
        <?php foreach ($heroStats as $stat): ?>
        <div class="hero-stat">
            <div class="hero-stat-value" style="<?= $stat['style'] ?? '' ?>"><?= $stat['value'] ?></div>
            <div class="hero-stat-label"><?= $stat['label'] ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
