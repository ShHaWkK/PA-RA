!function() {

    var today = moment();

    function Calendar(selector, events) {
        this.el = document.querySelector(selector);
        this.events = events;
        this.current = moment().date(1);
        this.draw();
        var current = document.querySelector('.today');
        if(current) {
            var self = this;
            window.setTimeout(function() {
                self.openDay(current);
            }, 500);
        }
    }

    Calendar.prototype.draw = function() {
        //Create Header
        this.drawHeader();

        //Draw Month
        this.drawMonth();

        this.drawLegend();
    }

    Calendar.prototype.drawHeader = function() {
        var self = this;
        if(!this.header) {
            //Create the header elements
            this.header = createElement('div', 'header');
            this.header.className = 'header';

            this.title = createElement('h1');

            var right = createElement('div', 'right');
            right.addEventListener('click', function() { self.nextMonth(); });

            var left = createElement('div', 'left');
            left.addEventListener('click', function() { self.prevMonth(); });

            //Append the Elements
            this.header.appendChild(this.title);
            this.header.appendChild(right);
            this.header.appendChild(left);
            this.el.appendChild(this.header);
        }

        this.title.innerHTML = this.current.format('MMMM YYYY');
    }

    Calendar.prototype.drawMonth = function() {
        var self = this;

        if(this.month) {
            this.oldMonth = this.month;
            this.oldMonth.className = 'month out ' + (self.next ? 'next' : 'prev');
            this.oldMonth.addEventListener('webkitAnimationEnd', function() {
                self.oldMonth.parentNode.removeChild(self.oldMonth);
                self.month = createElement('div', 'month');
                self.backFill();
                self.currentMonth();
                self.fowardFill();
                self.el.appendChild(self.month);
                window.setTimeout(function() {
                    self.month.className = 'month in ' + (self.next ? 'next' : 'prev');
                }, 16);
            });
        } else {
            this.month = createElement('div', 'month');
            this.el.appendChild(this.month);
            this.backFill();
            this.currentMonth();
            this.fowardFill();
            this.month.className = 'month new';
        }
    }

    Calendar.prototype.backFill = function() {
        var clone = this.current.clone();
        var dayOfWeek = clone.day();

        if(!dayOfWeek) { return; }

        clone.subtract('days', dayOfWeek+1);

        for(var i = dayOfWeek; i > 0 ; i--) {
            this.drawDay(clone.add('days', 1));
        }
    }

    Calendar.prototype.fowardFill = function() {
        var clone = this.current.clone().add('months', 1).subtract('days', 1);
        var dayOfWeek = clone.day();

        if(dayOfWeek === 6) { return; }

        for(var i = dayOfWeek; i < 6 ; i++) {
            this.drawDay(clone.add('days', 1));
        }
    }

    Calendar.prototype.currentMonth = function() {
        var clone = this.current.clone();

        while(clone.month() === this.current.month()) {
            this.drawDay(clone);
            clone.add('days', 1);
        }
    }

    Calendar.prototype.getWeek = function(day) {
        if(!this.week || day.day() === 0) {
            this.week = createElement('div', 'week');
            this.month.appendChild(this.week);
        }
    }

    Calendar.prototype.drawDay = function(day) {
        var self = this;
        this.getWeek(day);

        //Outer Day
        var outer = createElement('div', this.getDayClass(day));
        outer.addEventListener('click', function() {
            self.openDay(this);
        });

        //Day Name
        var name = createElement('div', 'day-name', day.format('ddd'));

        //Day Number
        var number = createElement('div', 'day-number', day.format('DD'));

        //Events
        var events = createElement('div', 'day-events');
        this.drawEvents(day, events);

        outer.appendChild(name);
        outer.appendChild(number);
        outer.appendChild(events);
        this.week.appendChild(outer);
    };

    Calendar.prototype.toggleNavigationButtons = function(enable) {
        var leftButton = document.querySelector('.left');
        var rightButton = document.querySelector('.right');

        if (leftButton && rightButton) {
            leftButton.disabled = !enable;
            rightButton.disabled = !enable;
        }
    }

    Calendar.prototype.nextMonth = function() {
        this.toggleNavigationButtons(false); // Désactiver les boutons
        this.current.add('months', 1);
        this.next = true;
        this.draw();
    }

    Calendar.prototype.prevMonth = function() {
        this.toggleNavigationButtons(false); // Désactiver les boutons
        this.current.subtract('months', 1);
        this.next = false;
        this.draw();
    }

    Calendar.prototype.drawEvents = function(day, element) {
        if (day.isSame(this.current, 'month')) {
            var todaysEvents = this.events.filter(ev => day.isSame(moment(ev.date), 'day'));

            // Ajouter les nouveaux événements
            todaysEvents.forEach(function(ev) {
                var evSpan = createElement('span', ev.color);
                element.appendChild(evSpan);
            });
        }
    };

    Calendar.prototype.getDayClass = function(day) {
        classes = ['day'];
        if(day.month() !== this.current.month()) {
            classes.push('other');
        } else if (today.isSame(day, 'day')) {
            classes.push('today');
        }
        return classes.join(' ');
    }

    Calendar.prototype.openDay = function(el) {
        var details, arrow;
        var dayNumber = +el.querySelectorAll('.day-number')[0].innerText || +el.querySelectorAll('.day-number')[0].textContent;
        var day = this.current.clone().date(dayNumber);

        var currentOpened = document.querySelector('.details');

        // Check to see if there is an open details box on the current row
        if(currentOpened && currentOpened.parentNode === el.parentNode) {
            details = currentOpened;
            arrow = document.querySelector('.arrow');
        } else {
            // Close the open events on different week row
            if(currentOpened) {
                currentOpened.addEventListener('webkitAnimationEnd', function() {
                    currentOpened.parentNode.removeChild(currentOpened);
                });
                currentOpened.addEventListener('oanimationend', function() {
                    currentOpened.parentNode.removeChild(currentOpened);
                });
                currentOpened.addEventListener('msAnimationEnd', function() {
                    currentOpened.parentNode.removeChild(currentOpened);
                });
                currentOpened.addEventListener('animationend', function() {
                    currentOpened.parentNode.removeChild(currentOpened);
                });
                currentOpened.className = 'details out';
            }

            // Create the Details Container
            details = createElement('div', 'details in');

            // Create the arrow
            arrow = createElement('div', 'arrow');

            // Create the event wrapper
            details.appendChild(arrow);
            el.parentNode.appendChild(details);
        }

        var todaysEvents = this.events.reduce(function(memo, ev) {
            if(ev.date.isSame(day, 'day')) {
                memo.push(ev);
            }
            return memo;
        }, []);

        this.renderEvents(todaysEvents, details);

        arrow.style.left = el.offsetLeft - el.parentNode.offsetLeft + 27 + 'px';
    }

    Calendar.prototype.renderEvents = function(events, ele) {
        // Remove any events in the current details element
        var currentWrapper = ele.querySelector('.events');
        var wrapper = createElement('div', 'events in' + (currentWrapper ? ' new' : ''));

        events.forEach(function(ev) {
            var div = createElement('div', 'event');
            var square = createElement('div', 'event-category ' + ev.color);
            var span = createElement('span', '', ev.eventName);

            // Ensure collectionId and date are properly defined
            var event_id = ev.id;
            var date = ev.date ? ev.date.format('YYYY-MM-DD') : 'defaultDate';

            // Determine the href based on the calendar type
            var href;
            switch (ev.calendar) {
                case 'Collectes':
                    href = `/Volunteer/Collection?collectionId=${event_id}&date=${date}`;
                    break;
                case 'Services':
                    href = `/Volunteer/Service?serviceRegistrationId=${event_id}`;
                    break;
                case 'Livraisons':
                    href = `/Volunteer/Distribution?routeId=${event_id}`;
                    break;
                default:
                    href = '#';
            }

            console.log("href", href); // Check href value

            // Create the link and set href
            var link = document.createElement('a');
            link.className = 'event-link';
            link.href = href;

            // Add span with text to the link
            link.appendChild(span);

            console.log("link", link); // Check created link

            div.appendChild(square); // Append the square outside the link
            div.appendChild(link);   // Append the link containing the span

            wrapper.appendChild(div); // Append the event div to the wrapper
        });

        if (!events.length) {
            var div = createElement('div', 'event empty');
            var span = createElement('span', '', 'No Events');

            div.appendChild(span);
            wrapper.appendChild(div);
        }

        if (currentWrapper) {
            currentWrapper.className = 'events out';
            currentWrapper.addEventListener('webkitAnimationEnd', function() {
                currentWrapper.parentNode.removeChild(currentWrapper);
                ele.appendChild(wrapper);
            });
            currentWrapper.addEventListener('oanimationend', function() {
                currentWrapper.parentNode.removeChild(currentWrapper);
                ele.appendChild(wrapper);
            });
            currentWrapper.addEventListener('msAnimationEnd', function() {
                currentWrapper.parentNode.removeChild(currentWrapper);
                ele.appendChild(wrapper);
            });
            currentWrapper.addEventListener('animationend', function() {
                currentWrapper.parentNode.removeChild(currentWrapper);
                ele.appendChild(wrapper);
            });
        } else {
            ele.appendChild(wrapper);
        }
    }

    Calendar.prototype.drawLegend = function() {
        var legend = createElement('div', 'legend');
        var calendars = this.events.map(function(e) {
            return e.calendar + '|' + e.color;
        }).reduce(function(memo, e) {
            if(memo.indexOf(e) === -1) {
                memo.push(e);
            }
            return memo;
        }, []).forEach(function(e) {
            var parts = e.split('|');
            var entry = createElement('span', 'entry ' +  parts[1], parts[0]);
            legend.appendChild(entry);
        });
        this.el.appendChild(legend);
    }

    window.Calendar = Calendar;

    function createElement(tagName, className, innerText) {
        var ele = document.createElement(tagName);
        if(className) {
            ele.className = className;
        }
        if(innerText) {
            ele.innerText = ele.textContent = innerText;
        }
        return ele;
    }

}();