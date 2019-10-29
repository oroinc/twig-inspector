/* eslint no-var: off */
(function() {
    var sfToolbar = document.getElementsByClassName('sf-toolbar')[0];

    function Template(name, link) {
        this.name = name;
        this.link = link;
    }

    function Block(index, element, templates) {
        this.index = index;
        this.element = element;
        this.templates = templates;
    }

    Block.prototype.toString = function() {
        var text = '';
        for (var i = 0; i < this.templates.length; i++) {
            var template = this.templates[i];
            if (text.length > 0) {
                text += '<br/>';
            }
            text += template.name;
        }
        return text;
    };

    function BlockStorage() {
        var elements = [];
        var templatesToElements = [];

        this.collectData = function() {
            elements = [];
            templatesToElements = [];

            var startComments = document.createNodeIterator(document.body, NodeFilter.SHOW_COMMENT);
            var curNode;
            while (curNode = startComments.nextNode()) {
                var match = curNode.nodeValue.match(/^(\s+[\u250F\u256D\u2554\u250E]([^\s]?)+\s)([^\s]+)(\s\[)([^\]]+)(\]\s#)(\w+)/);
                if (null === match) {
                    continue;
                }
                var templateId = match[7];
                var templateName = match[3] + ' [' + match[5].replace(/\/_template\//g, '') + ']';
                var templateLink = match[5];
                var element = curNode.nextSibling;
                var regexp = new RegExp('^(\\s+[\\u2517\\u2570\\u255A\\u2516])([^#]+)(#' + templateId + ')$', 'g');
                while (!(!element || (element.nodeType === 8 && element.nodeValue.match(regexp)))) {
                    if (
                        element.nodeType === 1 &&
                        ['SCRIPT', 'STYLE'].indexOf(element.tagName) === -1 &&
                        !sfToolbar.contains(element) &&
                        window.getComputedStyle(element)['display'] !== 'none'
                    ) {
                        var layoutItem = storage.findOrCreate(element);
                        var template = new Template(templateName, templateLink);
                        storage.addTemplate(layoutItem.index, template);
                    }
                    element = element.nextSibling;
                }
            }
        };

        this.find = function(element) {
            var index = elements.indexOf(element);
            if (index < 0) {
                return null;
            }

            var templates = templatesToElements[index];

            return new Block(index, element, templates);
        };

        this.create = function(element) {
            var length = elements.push(element);

            var index = length - 1;
            templatesToElements[index] = [];
            return new Block(index, element, []);
        };

        this.findOrCreate = function(element) {
            var layoutItem = this.find(element);

            if (null === layoutItem) {
                layoutItem = this.create(element);
            }

            return layoutItem;
        };

        this.addTemplate = function(index, template) {
            templatesToElements[index].push(template);
        };

        this.getTemplates = function(index) {
            return templatesToElements[index];
        };
    }

    function Overlay(storage, statusIcon) {
        this.isEnabled = false;

        this.block = document.createElement('DIV');
        this.block.id = '_twig_inspector__overlay__block';
        document.body.appendChild(this.block);

        this.info = document.createElement('DIV');
        this.info.id = '_twig_inspector__overlay__info';
        document.body.appendChild(this.info);

        this.hide = function() {
            this.info.classList.remove('_twig_inspector__visible');
            this.block.classList.remove('_twig_inspector__visible');
        };

        this.show = function(layoutItem) {
            var element = layoutItem.element;

            var width = element.offsetWidth;
            var height = element.offsetHeight;
            var left = element.getBoundingClientRect().left;
            var top = element.getBoundingClientRect().top + window.scrollY;

            this.block.style.width = width + 'px';
            if (height !== null) this.block.style.height = height + 'px';
            this.block.style.left = left + 'px';
            this.block.style.top = top + 'px';

            this.block.dataset.templateIndex = layoutItem.index;


            this.info.innerHTML = layoutItem.toString();

            if (top + height + 50 < window.innerHeight + window.scrollY) {
                this.info.style.top = top + height + 2 + 'px';
            } else {
                this.info.style.top = top - this.info.offsetHeight - 2 + 'px';
            }

            if (left + this.info.offsetWidth < window.innerWidth) {
                this.info.style.left = left + 'px';
                this.info.style.right = 'auto';
            } else {
                this.info.style.left = 'auto';
                this.info.style.right = 0;
            }

            this.block.classList.add('_twig_inspector__visible');
            this.info.classList.add('_twig_inspector__visible');
        };

        this.freeze = function() {
            this.info.classList.remove('_twig_inspector__visible');
            document.body.removeEventListener('mousemove', onMouseMove);
        };

        this.enable = function() {
            document.body.addEventListener('mousemove', onMouseMove);
            this.isEnabled = true;

            storage.collectData();
            statusIcon.classList.add('sf-toolbar-status-green');
            statusIcon.classList.remove('sf-toolbar-status-yellow');
        };

        this.reset = function() {
            this.freeze();
            this.info.classList.remove('_twig_inspector__visible');
            this.block.classList.remove('_twig_inspector__visible');
            this.block.classList.remove('_twig_inspector__overlay__block_static');
            this.block.innerHTML = '';
            statusIcon.classList.remove('sf-toolbar-status-green');
            statusIcon.classList.add('sf-toolbar-status-yellow');
            this.isEnabled = false;
        };

        /**
         * Event listeners
         **/
        var self = this;
        var lastFocusedElement;

        var onMouseMove = function(event) {
            var elements = document.elementsFromPoint(event.clientX, event.clientY);

            for (var i = 0; i < elements.length; i++) {
                var element = elements[i];
                if (sfToolbar.contains(element)) {
                    self.hide();
                }
                var layoutItem = storage.find(element);

                if (null !== layoutItem) {
                    if (lastFocusedElement === element) {
                        return;
                    }
                    lastFocusedElement = element;
                    return self.show(layoutItem);
                }
            }

            self.hide();
        };

        this.block.addEventListener('click', function(event) {
            if (self.block.classList.contains('_twig_inspector__overlay__block_static')) {
                return;
            }
            var templates = storage.getTemplates(self.block.dataset.templateIndex);

            if (templates.length === 1) {
                self.reset();
                window.location.href = templates[0].link;
                event.stopPropagation();
            } else {
                for (var i = 0; i < templates.length; i++) {
                    var template = templates[i];

                    var link = document.createElement('div');
                    link.dataset.href = template.link;
                    link.innerText = template.name;
                    link.addEventListener('click', function(event) {
                        self.reset();
                        window.location.href = this.dataset.href;
                        event.stopPropagation();
                    });
                    self.block.appendChild(link);
                    self.block.classList.add('_twig_inspector__overlay__block_static');
                }
                self.block.style.left = event.clientX - 20 + 'px';
                self.block.style.right = 'auto';
                self.block.style.top = event.clientY + window.scrollY - 20 + 'px';
            }

            self.freeze();

            event.stopPropagation();
        });

        document.onkeydown = function(evt) {
            evt = evt || window.event;
            if (evt.keyCode === 27) {
                overlay.reset();
            }
        };
    }

    var statusCheckbox = document.getElementById('_twig_inspector__status');
    if (!statusCheckbox) {
        return;
    }

    statusCheckbox.addEventListener('click', function(event) {
        var isActive = event.target.checked === true;
        document.cookie = 'twig_inspector_is_active=' + isActive + ';path=/';
        location.reload();
    });

    if (statusCheckbox.checked === false) {
        return;
    }

    var statusIcon = document.getElementById('_twig_inspector__icon');
    statusIcon.addEventListener('click', function(event) {
        if (overlay.isEnabled) {
            overlay.reset();
        } else {
            overlay.enable();
        }
    });

    var storage = new BlockStorage();
    var overlay = new Overlay(storage, statusIcon);
})();
