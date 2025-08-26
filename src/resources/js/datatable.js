import _ from 'lodash';

import { DataTable } from "simple-datatables";
import {defaultConfig} from "simple-datatables/src/config"
import { template } from "./template";


const DTDefault = _.assignIn(defaultConfig, {
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
        try {
            const response = await axios.post('/dt/export/excel/', data, {
                responseType: 'blob' // Importante para manejar archivos
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const a = document.createElement('a');
            a.href = url;
            a.download = name + '.xlsx';
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (error) {
            console.error('Error al exportar a Excel:', error);
        }
    },
    pdf: async function(data, name) {
        try {
            const response = await axios.post('/dt/export/pdf/', data, {
                responseType: 'blob' // Importante para manejar archivos
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const a = document.createElement('a');
            a.href = url;
            a.download = name + '.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (error) {
            console.error('Error al exportar a PDF:', error);
        }
    },
    formattedData: (data) => {
        return data.data.map(row => {
            return data.headings.reduce((obj, heading, index) => {
                obj[heading] = row[index];
                return obj;
            }, {});
        });
    }
};

export const getToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]').content;
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    return token;
}

const str2obj = (strs) => {
    try {
        const obj = JSON.parse(strs)
        return obj
    } catch(e) {
        return strs;
    }
}

export const DTLaravel = (selector, data, options) => {
    data = str2obj(data)
    const el = document.querySelector(selector)
    if (!el) { return; }
    const opt = _.assignIn(DTDefault, str2obj(options) );
    opt.data = data
    if (opt.caption == null) {
        opt.caption = undefined;
    }

    const DT = new DataTable(el, opt);

    DT.on("datatable.init", () => {
        setTimeout(() => {
            const WrapParent = el.closest(".datatable-wrapper");
            if (!WrapParent) return;
            const tableEl = WrapParent.querySelector("table");
            if (!tableEl) return;
            const UID = tableEl.id.replace("datatable-", "");

            if (_.hasIn(DT.options.labels, 'headers')) {
                const Headers = WrapParent.querySelectorAll("table thead tr th button");
                const Trans = DT.options.labels.headers;
                Headers.forEach((th) => {
                    const txt = th.textContent;
                    if (_.hasIn(Trans, txt)) {
                        th.dataset.text = txt;
                        th.textContent = Trans[txt];
                    }
                })
            }

            WrapParent.querySelector(".ex-excel")?.addEventListener("click", () => {
                api.excel(
                    DT.options.data,
                    tableEl.dataset.exportName
                );
            });

            WrapParent.querySelector(".ex-pdf")?.addEventListener("click", () => {
                api.pdf(
                    DT.options.data,
                    tableEl.dataset.exportName
                );
            });

            WrapParent.querySelector(".filterclean")?.addEventListener("click", () => {
                const inputs = WrapParent.querySelectorAll("input");
                inputs.forEach((input) => {
                    input.value = "";
                });
            });

            WrapParent.querySelector(".update")?.addEventListener("click", () => {
                location.reload();
            });

        }, 500)
    })
    let restor = {
        DT: DT,
        update: async (url) => {
            try {
                const res = await axios.get(url);
                DT.data = res.data;
                DT.update(true);
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


window.addEventListener("load", ()=> {
    const datatables = document.querySelectorAll('[data-dt]');
    if (datatables) {
        Array.from(datatables).forEach(dts => {
            const datas = dts.dataset.dt
            const options = dts.dataset.dtOptions
            const ID = dts.id
            const DTL = DTLaravel(dts, datas, options)
            if ('DT' in window) {
                window.DT[ID] = DTL;
            } else {
                window.DT = [];
                window.DT[ID] = DTL;
            }
            
        })
    }
})