const PROMPTS = {
    get_template: (thisClass) => {
        var json, html;
        html = document.createElement('div');html.classList.add('dynamic_popup');
        if(PROMPTS.lastJson) {
            html.innerHTML = PROMPTS.generate_template(thisClass);
        } else {
            html.innerHTML = `<div class="spinner-material"></div><h3>${thisClass.i18n?.pls_wait??'Please wait...'}</h3>`;
        }
        return html;
    },
    init_prompts: (thisClass) => {
        PROMPTS.core = thisClass;var card;
    },
    init_events: (thisClass) => {
        document.querySelectorAll('.popup_foot .button[data-react]').forEach((el) => {
            el.addEventListener('click', (event) => {
                event.preventDefault();
                switch (el.dataset.react) {
                    case 'back':
                        PROMPTS.do_pagination(false);
                        break;
                    default:
                        PROMPTS.do_pagination(true);
                        break;
                }
            });
        });
        PROMPTS.currentStep=(0);PROMPTS.do_pagination(true);
    },
    generate_template: (thisClass) => {
        var json, html;
        json = PROMPTS.lastJson;
        html = '';
        html += (json.header)?`
            ${(json.header.category_photo)?`<div class="header_image" style="background-image: url('${json.header.category_photo}');"></div>`:''}
        `:'';
        html += PROMPTS.generate_fields(thisClass);
        return html;
    },
    generate_fields: (thisClass) => {
        var div, node, step, foot, btn, back, fields = PROMPTS.get_data(thisClass);
        if(!fields && (thisClass.config?.buildPath??false)) {
            return '<img src="'+thisClass.config.buildPath+'/icons/undraw_file_bundle_re_6q1e.svg">';
        }
        div = document.createElement('div');node = document.createElement('form');
        node.action=thisClass.ajaxUrl;node.type='post';node.classList.add('popup_body');
        fields.forEach((field, i) => {
            step = PROMPTS.do_field(field);i++;
            step.dataset.step = field.fieldID;
            node.appendChild(step);
            PROMPTS.totalSteps=i;
        });
        foot = document.createElement('div');foot.classList.add('popup_foot');
        btn = document.createElement('button');btn.classList.add('btn', 'btn-primary', 'button');
        btn.type='button';btn.dataset.react='continue';
        btn.innerHTML=`<span>${thisClass.i18n?.continue??'Continue'}</span><div class="spinner-circular-tube"></div>`;
        back = document.createElement('button');back.classList.add('btn', 'btn-default', 'button');
        back.type='button';back.dataset.react = 'back';back.innerHTML=thisClass.i18n?.back??'Back';
        foot.appendChild(back);foot.appendChild(btn);div.appendChild(node);div.appendChild(foot);
        return div.innerHTML;
    },
    str_replace: (str) => {
        var data = thisClass.prompts.lastJson,
        searchNeedles = {'category.name': data.category.name};
        Object.keys(searchNeedles).forEach((needle)=> {
            str = str.replaceAll(needle, searchNeedles[needle]);
        });
        return str;
    },
    get_data: (thisClass) => {
        let fields = PROMPTS.lastJson.category.custom_fields;
        if(!fields || fields=='') {return false;}
        fields.forEach((row, i) => {row.orderAt = (i+1);});
        return fields;
    },
    do_field: (field) => {
        var fields, form, fieldset, input, level, span, option, head, others, body, div, info, i = 0;
        div = document.createElement('div');div.classList.add('popup_step', 'd-none');
        head = document.createElement('h2');head.innerHTML=PROMPTS.str_replace(field?.heading??'');
        div.appendChild(head);
        if((field?.subtitle??'')!='') {
            info = document.createElement('p');
            info.innerHTML=PROMPTS.str_replace(field?.subtitle??'');
            div.appendChild(info);
        }
        
        input = level = false;
        fieldset = document.createElement('fieldset');
        level = document.createElement('level');
        level.innerHTML = PROMPTS.str_replace(field?.label??'');
        level.setAttribute('for',`field_${i}`);
        
        switch (field.type) {
            case 'textarea':
                input = document.createElement('textarea');input.classList.add('form-control');
                input.name = 'field.'+field.fieldID;
                input.placeholder = PROMPTS.str_replace(field?.placeholder??'');
                input.id = `field_${i}`;input.innerHTML = field?.value??'';
                input.dataset.fieldId = field.fieldID;
                break;
            case 'input':case 'text':case 'number':case 'date':case 'time':case 'local':case 'color':case 'range':
                input = document.createElement('input');input.classList.add('form-control');
                input.name = 'field.'+field.fieldID;
                input.placeholder = PROMPTS.str_replace(field?.placeholder??'');
                input.id = `field_${i}`;input.type = (field.type=='input')?'text':field.type;
                input.value = field?.value??'';input.dataset.fieldId = field.fieldID;
                if(level) {fieldset.appendChild( level );}
                if(input) {fieldset.appendChild( input );}
                if(input || level) {div.appendChild( fieldset );}
                break;
            case 'select':
                input = document.createElement('select');input.classList.add('form-control');
                input.name = 'field.'+field.fieldID;input.id = `field_${i}`;
                input.dataset.fieldId = field.fieldID;
                field.options.forEach((opt,i)=> {
                    option = document.createElement('option');option.value=opt?.label??'';option.innerHTML=opt?.label??'';option.dataset.index = i;
                    input.appendChild(option);
                });
                if(level) {fieldset.appendChild( level );}
                if(input) {fieldset.appendChild( input );}
                if(input || level) {div.appendChild( fieldset );}
                break;
            case 'radio':case 'checkbox':
                input = document.createElement('div');input.classList.add('form-wrap');
                // field.options = field.options.reverse();
                field.options.forEach((opt, optI)=> {
                    level = document.createElement('label');level.classList.add('form-control');
                    // level.setAttribute('for', `field_${i}_${optI}`);
                    if(opt.input) {level.classList.add('form-flexs');}
                    span = document.createElement('span');
                    if(!opt.input) {span.innerHTML = opt.label;} else {
                        others = document.createElement('input');others.type='text';
                        others.name='field.'+field.fieldID+'.others';others.placeholder=opt.label;
                        others.dataset.fieldId = field.fieldID;others.dataset.index = optI;
                        span.appendChild(others);
                    }
                    option = document.createElement('input');option.value=opt.label;option.type=field.type;option.name='field.'+field.fieldID+'.option';
                    option.dataset.index = optI;option.dataset.fieldId = field.fieldID;
                    option.id=`field_${i}_${optI}`;
                    level.appendChild(option);level.appendChild(span);input.appendChild(level);
                    fieldset.appendChild(input);div.appendChild( fieldset );
                });
                break;
            default:
                input = level = false;
                break;
        }
        i++;
        return div;
    },
    do_submit: async (thisClass, el) => {
        var data = thisClass.generate_formdata(el);
        var args = thisClass.lastReqs = {
            best_of: 1,frequency_penalty: 0.01,presence_penalty: 0.01,top_p: 1,
            max_tokens: parseInt( data?.max_tokens??700 ),temperature: 0.7,model: data?.model??"text-davinci-003",
        };
        try {
            args.prompt = thisClass.str_replace(
                Object.keys(data).map((key)=>'{{'+key+'}}'),
                Object.values(data),
                thisClass.popup.thefield?.syntex??''
            );
            PROMPTS.lastJson = await thisClass.openai.createCompletion( args );
            var prompt = thisClass.popup.generate_results( thisClass );
            document.querySelector('#the_generated_result').value = prompt;
            // console.log( prompt );
        } catch (error) {
            thisClass.openai_error( error );
        }
    },
    do_pagination: async (plus) => {
        var step, root, header, field, back, submit;PROMPTS.currentStep = PROMPTS?.currentStep??0;
        root = '.fwp-swal2-popup .popup_body .popup_step';
        if(await PROMPTS.beforeSwitch(thisClass, plus)) {
            PROMPTS.currentStep = (plus)?(
                (PROMPTS.currentStep < PROMPTS.totalSteps)?(PROMPTS.currentStep+1):PROMPTS.currentStep
            ):(
                (PROMPTS.currentStep > 0)?(PROMPTS.currentStep-1):PROMPTS.currentStep
            );
            if(PROMPTS.currentStep<=0) {return;}
            back = document.querySelector('.popup_foot .button[data-react="back"]');
            if(back && back.classList) {
                if(!plus && PROMPTS.currentStep<=1) {back.classList.add('invisible');} else {back.classList.remove('invisible');}
            }
            
            field = PROMPTS.lastJson.category.custom_fields[PROMPTS.currentStep];
            header = document.querySelector('.header_image');
            if(header && field && field.headerbgurl!='') {
                jQuery(document.querySelector('.header_image')).css('background-image', 'url('+field.headerbgurl+')');
            }
            document.querySelectorAll(root+'.step_visible').forEach((el)=>{el.classList.add('d-none');el.classList.remove('step_visible');});
            step = document.querySelector(root+'[data-step="'+PROMPTS.currentStep+'"]');
            if(step) {
                if(!plus) {step.classList.add('popup2left');}
                step.classList.remove('d-none');setTimeout(()=>{step.classList.add('step_visible');},300);
                if(!plus) {setTimeout(()=>{step.classList.remove('popup2left');},1500);}
            }
        } else {
            if(PROMPTS.currentStep >= PROMPTS.lastJson.category.custom_fields.length) {
                console.log('Do Submit');
                submit = document.querySelector('.popup_foot .button[data-react="continue"]');
                if(submit && submit.classList) {
                    submit.setAttribute('disabled', true);

                    var formdata = new FormData();
                    formdata.append('action', 'futurewordpress/project/ajax/submit/popup');
                    formdata.append('dataset', await JSON.stringify(thisClass.generate_formdata(document.querySelector('.popup_body'))));
                    formdata.append('_nonce', thisClass.ajaxNonce);
                    thisClass.sendToServer(formdata);

                    setTimeout(() => {submit.removeAttribute('disabled');}, 100000);
                }
            } else {
                console.log('Proceed failed');
            }
        }
    },
    beforeSwitch: (thisClass, plus) => {
        var back, next, elem, last;last = elem = false;
        // console.log(PROMPTS.totalSteps, PROMPTS.currentStep);
        if(plus) {
            elem = document.querySelector('.popup_body .popup_step[data-step="'+PROMPTS.currentStep+'"]');
            elem = (elem)?parseInt(elem.nextElementSibling.dataset?.step??0):0;
            if(elem>0 && (PROMPTS.currentStep+1) < elem) {
                last = PROMPTS.currentStep;
                PROMPTS.currentStep = (elem-1);
            }
        }
        if(plus && PROMPTS.totalSteps!=0 && PROMPTS.totalSteps<=PROMPTS.currentStep) {
            // Submitting popup!
            if(elem) {PROMPTS.currentStep = last;}
            return (PROMPTS.totalSteps != PROMPTS.currentStep);
        }
        if(plus) {
            var data = thisClass.generate_formdata( document.querySelector('.popup_body') );
            var step = document.querySelector('.popup_step.step_visible'), prev = [];
            if(!step) {return (PROMPTS.currentStep<=0);}
            if(!PROMPTS.validateField(step, data, thisClass)) {return false;}

            step.querySelectorAll('input, select').forEach((el,ei)=>{
                // el is the element input
                if(!prev.includes(el.name) && data[el.name] && data[el.name]==el.value) {
                    // item is the fieldset
                    var item = PROMPTS.lastJson.category.custom_fields.find((row, i)=>row.fieldID==el.dataset.fieldId);
                    if(item) {
                        // opt is the options
                        var opt = (item?.options??[]).find((opt,i)=>i==el.dataset.index);
                        if(opt) {
                            prev.push(el.dataset.index);
                            // if(item.required) {return false;}
                            if(!item.is_conditional && opt.next && opt.next!='') {
                                next = PROMPTS.lastJson.category.custom_fields.find((row)=>row.fieldID==parseInt(opt.next));
                                if(next) {
                                    next.returnStep = item.fieldID;
                                    PROMPTS.currentStep = (next.fieldID-1);
                                    return true;
                                }
                            } else {
                                // return false;
                            }
                        }
                    }
                }
                return true;
            });
        }
        if(!plus) {
            var current = thisClass.prompts.lastJson.category.custom_fields.find((field)=>field.fieldID==PROMPTS.currentStep);
            var returnStep = current?.returnStep??false;
            var next = thisClass.prompts.lastJson.category.custom_fields.find((field)=>field.fieldID==returnStep);
            if(returnStep && next) {
                PROMPTS.currentStep = (parseInt(returnStep)+1);
                current.returnStep=false;
                return true;
            }
        }
        return true;
        // setTimeout(()=>{return true;},100);
    },
    validateField: (step, data, thisClass) => {
        // data = thisClass.generate_formdata(document.querySelector('.popup_body'));
        var fieldValue, field;fieldValue = step.querySelector('input, select');
        fieldValue = (fieldValue)?fieldValue?.name??false:false;
        field = PROMPTS.lastJson.category.custom_fields.find((row)=>row.fieldID==step.dataset.step);

        // console.log(step, data);
        // console.log([field, fieldValue], data[fieldValue]);

        thisClass.Swal.resetValidationMessage();
        switch (field?.type??false) {
            case 'text':case 'number':case 'color':case 'date':case 'time':case 'local':case 'range':case 'checkbox':case 'radio':
                if(!data[fieldValue] || data[fieldValue]=='') {
                    thisClass.Swal.showValidationMessage('You can\'t leave it blank.');
                    return false;
                }
                break;
            default:
                return true;
                break;
        }
        // thisClass.Swal.showValidationMessage('The telephone number you entered is not a valid number');
        return true;
    }
};
export default PROMPTS;