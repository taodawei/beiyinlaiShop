function galleryDrag(id){
    var node = document.querySelector(id);
                
    var draging = null;
    //使用事件委托，将li的事件委托给ul
    node.ondragstart = function(event) {
            //console.log("start");
                //firefox设置了setData后元素才能拖动！！！！
            event.dataTransfer.setData("te", event.target.innerText); //不能使用text，firefox会打开新tab
            
            draging = event.target;
            
            if (draging.nodeName !== "LI") {
                draging = draging.parentNode
            }
        }
    node.ondragover = function(event) {
        //console.log("onDrop over");
        event.preventDefault();
        var target = event.target;                                
        // var target = event.currentTarget;
        
        //因为dragover会发生在ul上，所以要判断是不是li                    
        if (target.nodeName !== "LI") {
            target = target.parentNode
        }
        if(target.nodeName === 'DIV'){
            return false;
        }
        if (target !== draging) {
            var targetRect = target.getBoundingClientRect();
            var dragingRect = draging.getBoundingClientRect();
            
            if (target) {
                if (target.animated) {
                    return false;
                }
                if(!target.classList.contains('gallery-item')){
                    return false;
                }
            }
            
            if (_index(draging) < _index(target)) {
                target.parentNode.insertBefore(draging, target.nextSibling);
            } else {
                target.parentNode.insertBefore(draging, target);
            }
            _animate(dragingRect, draging);
            _animate(targetRect, target);
            
            _imgArr = [];
            node.querySelectorAll('li img').forEach(function(v,k){
                
                // if(v.parentNode.className == 'gallery-item'){
                if(v.parentNode.classList.contains('gallery-item')){
                    _imgArr.push(v.getAttribute('src').replace('?x-oss-process=image/resize,w_122',''))
                }
            })
            document.querySelector('#originalPic').value = _imgArr.join('|')
        }
    }
    //获取元素在父元素中的index
function _index(el) {
        var index = 0;

        if (!el || !el.parentNode) {
            return -1;
        }

        while (el && (el = el.previousElementSibling)) {
            //console.log(el);
            index++;
        }

        return index;
    }

function _animate(prevRect, target) {
        var ms = 300;

        if (ms) {
            var currentRect = target.getBoundingClientRect();

            if (prevRect.nodeType === 1) {
                prevRect = prevRect.getBoundingClientRect();
            }

            _css(target, 'transition', 'none');
            _css(target, 'transform', 'translate3d(' +
                (prevRect.left - currentRect.left) + 'px,' +
                (prevRect.top - currentRect.top) + 'px,0)'
            );

            target.offsetWidth; // 触发重绘
            //放在timeout里面也可以
            // setTimeout(function() {
            //     _css(target, 'transition', 'all ' + ms + 'ms');
            //     _css(target, 'transform', 'translate3d(0,0,0)');
            // }, 0);
            _css(target, 'transition', 'all ' + ms + 'ms');
            _css(target, 'transform', 'translate3d(0,0,0)');

            clearTimeout(target.animated);
            target.animated = setTimeout(function() {
                _css(target, 'transition', '');
                _css(target, 'transform', '');
                target.animated = false;
            }, ms);
        }
    }
    //给元素添加style
    function _css(el, prop, val) {
        var style = el && el.style;

        if (style) {
            if (val === void 0) {
                if (document.defaultView && document.defaultView.getComputedStyle) {
                    val = document.defaultView.getComputedStyle(el, '');
                } else if (el.currentStyle) {
                    val = el.currentStyle;
                }

                return prop === void 0 ? val : val[prop];
            } else {
                if (!(prop in style)) {
                    prop = '-webkit-' + prop;
                }

                style[prop] = val + (typeof val === 'string' ? '' : 'px');
            }
        }
    }
}