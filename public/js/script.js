/* ===== SMMP Platform JS ===== */
$(function() {

    function setSidebarState(expanded) {
        var $sidebar = $('#sidebar');
        var $main = $('#mainContent');
        if (expanded) {
            $sidebar.removeClass('collapsed').addClass('expanded');
            $main.addClass('expanded');
        } else {
            $sidebar.removeClass('expanded').addClass('collapsed');
            $main.removeClass('expanded');
        }
    }
        // Save preference
        $.get('users/sidebar&state=' + (expanded ? 'expanded' : 'collapsed'));
    }

    // Sidebar toggle (desktop)
    $('#sidebarToggle').on('click', function(e) {
        e.preventDefault();
        var isCollapsed = $('#sidebar').hasClass('collapsed');
        setSidebarState(isCollapsed);
    });

    // Sidebar overlay close
    $('.sidebar-overlay').on('click', function() {
        $('#sidebar').removeClass('open');
        $(this).removeClass('show');
    });

    // Mobile sidebar toggle
    $('#mobileSidebarToggle').on('click', function(e) {
        e.preventDefault();
        $('#sidebar').addClass('open');
        $('.sidebar-overlay').addClass('show');
    });

    // --- Theme Toggle ---
    $('#themeToggle').on('click', function(e) {
        e.preventDefault();
        var $body = $('body');
        var isLight = $body.hasClass('light');
        var newTheme = isLight ? 'dark' : 'light';

        $body.toggleClass('light');

        var icon = newTheme === 'dark' ? 'sun' : 'moon';
        $('#themeToggle i').attr('class', 'fas fa-' + icon);

        $.get('users/theme&theme=' + newTheme);
    });

    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert-smm').fadeOut(400);
    }, 5000);

    // Animated counter
    function animateCounter(el) {
        var target = parseInt($(el).data('target')) || 0;
        if (target === 0) { $(el).text('0'); return; }
        var duration = 1000;
        var steps = 25;
        var stepVal = target / steps;
        var current = 0;
        var interval = setInterval(function() {
            current += stepVal;
            if (current >= target) { current = target; clearInterval(interval); }
            $(el).text(Math.floor(current).toLocaleString());
        }, duration / steps);
    }

    $('.stat-counter').each(function() {
        var $this = $(this);
        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        animateCounter($this[0]);
                        observer.unobserve($this[0]);
                    }
                });
            }, { threshold: 0.2 });
            observer.observe($this[0]);
        } else {
            animateCounter($this[0]);
        }
    });

    // Copy to clipboard
    $('[data-copy]').on('click', function() {
        var text = $(this).data('copy');
        navigator.clipboard.writeText(text).then(function() {
            var btn = $(this);
            var orig = btn.html();
            btn.html('<i class="fas fa-check"></i>');
            setTimeout(function() { btn.html(orig); }, 1500);
        }.bind(this)).catch(function() {
            alert('Failed to copy');
        });
    });

    // Table search filter
    $('.table-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        var table = $(this).closest('.card-smm').find('table');
        table.find('tbody tr').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(val) > -1);
        });
    });

    // Chart defaults
    if (typeof Chart !== 'undefined') {
        Chart.defaults.color = '#9aa0b0';
        Chart.defaults.borderColor = '#2a3040';
        Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    }

    // Responsive sidebar
    $(window).on('resize', function() {
        if ($(window).width() > 768) {
            $('.sidebar-overlay').removeClass('show');
            $('#sidebar').removeClass('open');
        }
    });

});