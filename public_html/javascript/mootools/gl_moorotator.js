var gl_mooRotator = new Class({

    Implements: [Options, Events],
    
    options: {
        controls: true,
        duration: 1000,
        delay: 5000,
        autoplay: false,
        blankimage: '{site_url}/images/speck.gif'
    },
    initialize: function (a, b) {
        this.container = $(a);
        this.setOptions(b);
        this.images = this.container.getElements('.gl_moorotatorimage');
        this.content = this.container.getElements('.gl_moorotatortext');
        this.current = 0;
        this.build();
        this.attachEvents();
        this.status = 'pause';
        if (this.options.autoplay) window.addEvent('load', this.play.bind(this));
        return this
    },
    build: function () {
        var b = this;
        $$(this.content, this.images).setStyle('position', 'absolute');
        var c = this.images.slice(1);
        var d = this.content.slice(1);
        c.each(function (a) {
            a.injectAfter(this.images[0]).setStyle('opacity', 0)
        },
        this);
        d.each(function (a) {
            a.injectAfter(this.content[0]).setStyle('opacity', 0)
        },
        this);
        var e = $$('.gl_moorotator').slice(1);
        e.each(function (a) {
            a.empty().dispose()
        });
        if (this.options.controls == 1) {
            var f = new Element('div', {
                'class': 'controls'
            }).inject(this.container);
        } else {
            var f = new Element('div', {
                'class': ''
            }).inject(this.container);
        }
        this.arrowPrev = new Element('img', {
            'class': 'control-prev',
            'title': '{prev}',
            'alt': '{prev}',
            'src': this.options.blankimage
        }).inject(f);
        this.arrowPlay = new Element('img', {
            'id': 'play-pause',
            'class': 'control-pause',
            'title': '{playpause}',
            'alt': '{playpause}',
            'src': this.options.blankimage
        }).inject(f);
        this.arrowNext = new Element('img', {
            'class': 'control-next',
            'title': '{next}',
            'alt': '{next}',
            'src': this.options.blankimage
        }).inject(f);
        if (this.options.corners) {
            (this.images.length).times(function (i) {
                (2).times(function (j) {
                    new Element('div', {
                        'class': 'i' + (j + 1)
                    }).inject(this.images[i])
                }.bind(this))
            }.bind(this))
        } (4).times(function (i) {
            new Element('div', {
                'class': 'corner c' + (i + 1)
            }).inject(this.content[0].getParent())
        }.bind(this));
        this.fx = [];
        (this.content.length).times(function (i) {
            this.fx[i] = [new Fx.Tween(this.images[i], {
                property: 'opacity',
                duration: this.options.duration,
                onStart: function () {
                    b.transitioning = true
                },
                onComplete: function () {
                    b.transitioning = false
                }
            }), new Fx.Tween(this.content[i], {
                property: 'opacity',
                duration: this.options.duration
            })]
        }.bind(this));
        return this
    },
    attachEvents: function () {
        var a = this,
        playstop = $('play-pause');
        this.arrowPrev.addEvent('click', this.previous.bind(this));
        this.arrowNext.addEvent('click', this.next.bind(this));
        this.arrowPlay.addEvent('click', function () {
            if (a.status == 'play') {
                a.stop();
                playstop.className = 'control-play'
            } else {
                a.play();
                playstop.className = 'control-pause'
            }
        });
        return this
    },
    previous: function () {
        if (this.transitioning) return this;
        var b = (!this.current) ? this.content.length - 1 : this.current - 1;
        this.fx[this.current].each(function (a) {
            a.start(0)
        });
        this.fx[b].each(function (a) {
            a.start(1)
        });
        this.current = b;
        return this
    },
    next: function () {
        if (this.transitioning) return this;
        var b = (this.current == this.content.length - 1) ? 0 : this.current + 1;
        this.fx[this.current].each(function (a) {
            a.start(0)
        });
        this.fx[b].each(function (a) {
            a.start(1)
        });
        this.current = b;
        return this
    },
    play: function () {
        if (this.status == 'play') return this;
        this.status = 'play';
        this.timer = this.next.periodical(this.options.delay + this.options.duration, this);
        return this
    },
    stop: function () {
        this.status = 'pause';
        $clear(this.timer);
        return this
    }
});

