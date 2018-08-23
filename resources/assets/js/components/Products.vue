<template lang="html">
  <div class="chat-composer">

      <div class="form-group">
                    <label for="csv_file" class="control-label col-sm-3 text-right">CSV file to import</label>
                    <div class="col-sm-9">
                        <input type="file" id="csv_file" name="csv_file" class="form-control" @change="loadCSV($event)">
                    </div>
    </div>


    <table v-if="parse_csv">
          <thead>
            <tr>
              <th v-for="key in parse_header"
                  @click="sortBy(key)"
                  :class="{ active: sortKey == key }">
                    {{ key | capitalize }}
                <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'">
                </span>
              </th>
            </tr>
          </thead>
          <tr v-for="csv in parse_csv">
            <td v-for="key in parse_header">
              {{csv[key]}}
            </td>
          </tr>

    </table>

  </div>
</template>

<script>
export default {
    data() {
        return {
            channel_name: '',
            channel_fields: [],
            channel_entries: [],
            parse_header: [],
            parse_csv: [],
            sortOrders:{},
            sortKey: ''
        }
    },
    methods: {
        csvJSON(csv){
            var vm = this
            var lines = csv.split("\n")
            var result = []
            var headers = lines[0].split(",")
            vm.parse_header = lines[0].split(",")
            lines[0].split(",").forEach(function (key) {
                vm.sortOrders[key] = 1
            })

            lines.map(function(line, indexLine){
                if (indexLine < 1) return // Jump header line

                var obj = {}
                var currentline = line.split(",")

                headers.map(function(header, indexHeader){
                    obj[header] = currentline[indexHeader]
                })

                result.push(obj)
            })

            result.pop() // remove the last item because undefined values

            return result // JavaScript object
        },
        loadCSV(e) {
            var vm = this
            if (window.FileReader) {
                var reader = new FileReader();
                reader.readAsText(e.target.files[0]);
                // Handle errors load
                reader.onload = function(event) {
                    var csv = event.target.result;
                    vm.parse_csv = vm.csvJSON(csv)
                    console.log(vm.parse_csv)

                    axios.post('/upload_csv', {'parse_csv':vm.parse_csv}).then(response => {
                        // Do whatever;
                    })

                };
                reader.onerror = function(evt) {
                    if(evt.target.error.name == "NotReadableError") {
                        alert("Canno't read file !");
                    }
                };
            } else {
                alert('FileReader are not supported in this browser.');
            }
        }
    }
}
</script>

<style lang="css">

    .panel-heading h1, .panel-heading h2, .panel-heading h3, .panel-heading h4, .panel-heading h5, .panel-heading h6 {
        margin: 0;
        padding: 0;
    }
    .panel-body .checkbox-inline {
        padding: 15px 20px;
    }
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
