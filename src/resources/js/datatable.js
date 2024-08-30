import "@rep985/fascinots";

import { DataTable } from "simple-datatables";
import {defaultConfig} from "simple-datatables/src/config"
import { template } from "./template";


const DTDefault = _$.assignIn(defaultConfig, {
    classes: {
        active: "active",
        disabled: "disabled",
        selector: "form-select",
        paginationList: "pagination",
        paginationListItem: "page-item",
        paginationListItemLink: "page-link"
    },
    template: template,
    tableRender: (_data, table, _type) => {
        const thead = table.childNodes[0];
        
        thead.childNodes.forEach((th) => {
            if (!th.attributes) {
                th.attributes = {}
            }
            th.attributes.scope = "col"
            const innerHeader = th.childNodes[0]
            if (!innerHeader.attributes) {
                innerHeader.attributes = {}
            }
            let innerHeaderClass = innerHeader.attributes.class ? `${innerHeader.attributes.class} th-inner` : "th-inner"

            if (innerHeader.nodeName === "a") {
                innerHeaderClass += " sortable sortable-center both"
                if (th.attributes.class?.includes("desc")) {
                    innerHeaderClass += " desc"
                } else if (th.attributes.class?.includes("asc")) {
                    innerHeaderClass += " asc"
                }
            }
            innerHeader.attributes.class = innerHeaderClass
            
        });
        const filterHeaders = {
            nodeName: "TR",
            childNodes: thead.childNodes[0].childNodes.map(
                (_th, index) => ({nodeName: "TH",
                    childNodes: [
                        {
                            nodeName: "INPUT",
                            attributes: {
                                class: "datatable-input form-control form-control-sm",
                                type: "search",
                                "data-columns": `[${index}]`
                            }
                        }
                    ]})
            )
        }
        thead.childNodes.push(filterHeaders)

        return table;
    }
});

const api = {
    excel: async function(data, name) {
        return await fetch('/dt/export/excel/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getToken()
            },
            body: JSON.stringify(data)
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = name+'.xlsx'; // Puedes ajustar el nombre del archivo aquí
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    },
    pdf: async function(data,name) {
        return await fetch('/dt/export/pdf/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getToken()
            },
            body: JSON.stringify(data)
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = name+'.pdf'; // Puedes ajustar el nombre del archivo aquí
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    },
    formattedData: (data) => {
        return data.data.map(row => {
            return data.headings.reduce((obj, heading, index) => {
                obj[heading] = row[index];
                return obj;
            }, {});
        })
    }
};

export const getToken = () => {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

export const DTLaravel = (el, data, options) => {
    const opt = _$.assignIn(DTDefault, options);
    opt.data = data
    if (opt.caption == null) {
        opt.caption = undefined;
    }


    const DT = new DataTable(el, opt);
    DT.on("datatable.init", () => {
        setTimeout(() => {
            const WrapParent = _$(el).parents(".datatable-wrapper");
            const UID = _$("table", WrapParent.first).attr("id").replace("datatable-", "")
            if (_$.hasIn(DT.options.labels, 'headers')) {
                const Headers = _$("table thead tr th button", WrapParent.first)
                const Trans = DT.options.labels.headers;
                Headers.each((th) => {
                    const txt = _$(th).text()
                    if (_$.hasIn(Trans, txt)) {
                        _$(th).data("text", txt)
                        _$(th).text(Trans[txt])
                    }
                })
            }
            _$(".ex-excel", WrapParent.first).click(function() {
                api.excel(
                    DT.options.data,
                    _$("table", WrapParent.first).data("export-name")
                )
            })
            _$(".ex-pdf", WrapParent.first).click(function() {
                api.pdf(
                    DT.options.data,
                    _$("table", WrapParent.first).data("export-name")
                )
            })
            _$(".filterclean", WrapParent.first).click(function(){
                _$("input", WrapParent.first).each((input) => {
                    input.value = ""
                })
            })
            _$(".update", WrapParent.first).click(function(){
                location.reload()
            })
        }, 500)
    })
    let restor = {
        DT: DT,
        update: async (url) => {
            try {
                const res = await fetch(url)
                DT.data = await res.json()
                DT.update(true)
            } catch (error) {
                console.error('Error al obtener los datos:', error);
            }
        },
        destroy: () => DT.destroy(),
        addRow: (data) => {
            DT.rows.add(data)
        },
        filters: function (criteria) {
            throw new Error("Function not implemented.");
        }
    }
    return restor;
}

window.DTLaravel = DTLaravel;