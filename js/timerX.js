(function ($) {
	var timerProfilerClass = function () {
		var instances = {},
		object = {
			instances: function () {
				return instances;
			},
			add: function (timer) {
				if (!object.has(timer)) {
					if (!object.instances[timer.index()]) {
						object.instances[timer.index()] = timer;
					}
				}

				return timer;
			},
			remove: function (timer) {
				if (this.has(timer)) {
					delete object.instances[timer.index()];
				}

				return timer;
			},
			contains: function (index) {
				if (this.instances[index]) {
					return true;
				}

				return false;
			},
			has: function (timer) {
				if (this.instances[timer.index()]) {
					return true;
				}

				return false;
			},
			slice: function (timer) {
				try {
					if (timer.state() === "ERROR" && timer.error()) {
						console.log('timerClass object entered an error state: ' + timer.error());
						console.log(object.profile(timer));
					}
				}
				catch (e) {
				}

				return timer;
			},
			profile: function (timer) {
				var p = {};

				$(timer).each(function (index) {
					var fn = timer[index];
					if (fn !== 'start' && fn !== 'stop' && fn !== 'crash' && fn !== 'pause') {
						p[fn] = timer[fn]();
					}
				});

				return p;
			},
			count: function () {
				return object.instances.length;
			}
		};

		return object;
	},
	timerClass = function (Ioptions) {
		if (!Ioptions) {
			Ioptions = {};
		}

		var Idefaults = {
			parent: $(document),
			delay: 0,
			interval: 0,
			repeat: false,
			callback: function (timer, args) { },
			args: {}
		},
		Iopt = $.extend(true, Idefaults, Ioptions),
		Iparent = Iopt.parent,
		Idelay = Iopt.delay,
		Iinterval = Iopt.interval,
		Irepeat = Iopt.repeat,
		Icallback = Iopt.callback,
		Iargs = Iopt.args,
		Iindex = null,
		Ierror = null,
		Istate = "OK",
		Itid = 0,
		Iticks = 0,
		Icurrent = new Date(),
		Inext = new Date(Icurrent.getTime() + Idelay),
		Ilast,
		Irunning = false,
		Ipaused = false,
		Iobject = null;

		Iobject = {
			end: function () {
				return Iparent;
			},
			parent: function () {
				return Iparent;
			},
			delay: function (val) {
				if (!val) {
					return Idelay;
				}
				else {
					Idelay = val;
					return Iobject;
				}
			},
			interval: function (val) {
				if (!val) {
					return Iinterval;
				}
				else {
					Iinterval = val;
					return Iobject;
				}
			},
			repeat: function (val) {
				if (!val) {
					return Irepeat;
				}
				else {
					Irepeat = val;
					return Iobject;
				}
			},
			callback: function (val) {
				if (!val) {
					return Icallback;
				}
				else {
					Icallback = val;
					return Iobject;
				}
			},
			args: function (val) {
				if (!val) {
					return Iargs;
				}
				else {
					Iargs = val;
					return Iobject;
				}
			},
			error: function () {
				return Ierror;
			},
			state: function () {
				return Istate;
			},
			tid: function () {
				return Itid;
			},
			ticks: function () {
				return Iticks;
			},
			current: function () {
				return Icurrent;
			},
			next: function () {
				return Inext;
			},
			last: function () {
				return Ilast;
			},
			running: function () {
				return Irunning;
			},
			paused: function () {
				return Ipaused;
			},
			index: function () {
				try {
					if (!Iindex) {
						var release = false,
							idx = null;

						while (!release) {
							idx = new Date().getTime().toString();

							if ($.timerProfiler.contains(idx) === false) {
								Iindex = idx;
								release = true;
							}
						}
					}
				}
				catch (e) {
					Iobject.crash(e);
				}

				return Iindex;
			},
			crash: function (exception) {
				Istate = "ERROR";
				Ierror = exception;

				return Iobject;
			},
			start: function () {
				try {
					Irunning = true;
					Iobject.register();

					if (Ipaused === false) {
						if (Iticks > 0) {
							Itid = setTimeout(Iobject.slice, Iinterval);
						}
						else {
							Itid = setTimeout(Iobject.slice, Idelay);
						}
					}
				}
				catch (e) {
					Iobject.crash(e);
				}

				return Iobject;
			},
			stop: function () {
				try {
					Iobject.pause();
					Iobject.unregister();
					clearTimeout(Itid);
				}
				catch (e) {
					Iobject.crash(e);
				}

				return Iobject;
			},
			pause: function () {
				try {
					Irunning = false;
					Ipaused = true;
				}
				catch (e) {
					Iobject.crash(e);
				}

				return Iobject;
			},
			tick: function () {
				Iticks += 1;

				Icurrent = new Date();

				if (Irepeat === true || Irepeat > 0) {
					Inext = new Date(Icurrent.getTime() + Iinterval);
				}
				else {
					Inext = null;
				}

				try {
					var cb = Iobject.callback();
					cb(Iobject, Iargs);
					Ilast = Icurrent;
				}
				catch (e) {
					Iobject.crash(e);
				}

				return Iobject;
			},
			slice: function () {
				try {
					$.timerProfiler.slice(Iobject);
					Iobject.tick();

					if (Irepeat === false || Irepeat <= 0) {
						Iobject.stop();
					}
					else if (Irepeat === true || Iticks < Irepeat) {
						Iobject.start();
					}
				}
				catch (e) {
					Iobject.crash(e);
				}

				return Iobject;
			},
			register: function () {
				$.timerProfiler.add(Iobject);
			},
			unregister: function () {
				$.timerProfiler.remove(Iobject);
			}
		};

		return Iobject;
	};

	$.extend({
		timerProfiler: timerProfilerClass()
	});

	$.extend({
		timerCreate: function (options) {
			if (!options) {
				options = {
					parent: $(document)
				};
			}
			else if (!options.parent) {
				options.parent = $(document);
			}

			return timerClass(options);
		},
		timerDelayCall: function (options) {
			if (!options) {
				options = {
					parent: $(document)
				};
			}
			else if (!options.parent) {
				options.parent = $(document);
			}

			return timerClass(options).start();
		}
	});

	$.fn.extend({
		timerCreate: function (options) {
			if (this !== jQuery) {
				if (!options) {
					options = {
						parent: $(this)
					};
				}
				else if (!options.parent) {
					options.parent = $(this);
				}
			}

			return timerClass(options);
		},
		timerDelayCall: function (options) {
			if (this !== jQuery) {
				if (!options) {
					options = {
						parent: $(this)
					};
				}
				else if (!options.parent) {
					options.parent = $(this);
				}
			}

			return timerClass(options).start();
		}
	});
} (jQuery));