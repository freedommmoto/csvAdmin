<template lang="html">
  <div class="chat-composer">

      <div class="form-group">
                    <label for="csv_file" class="control-label col-sm-3 text-right">CSV file to import</label>
                    <div class="col-sm-7">
                        <input type="file" id="csv_file" name="csv_file" class="form-control" @change="loadCSV($event)">
                    </div>
                    <div class="col-sm-2">
                        <a href="./download_csv"><button class="btn-primary">export</button></a>
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
            parse_header: ['id','Name','Description','Price','Stock'],
            parse_csv: [],
            requestCSv: [],
            sortOrders:{},
            sortKey: ''
        }
    },
    created() {
        Echo.join('chatroom')
            .here((users) => {
                this.usersInRoom = users;
            })
            .joining((user) => {
                this.usersInRoom.push(user);
            })
            .leaving((user) => {
                this.usersInRoom = this.usersInRoom.filter(u => u != user)
            })
            .listen('MessagePosted', (e) => {
                swal("New Files Been upload!", "Have new data been export", "success")
                console.log(e.message.message)
                this.getProducts()
            });
        this.getProducts()
    },
    methods: {
        getProducts(){
            fetch('/products')
                .then(res => res.json())
                .then(res => {
                    this.parse_csv = res;
                })
        },
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
                    vm.requestCSv = vm.csvJSON(csv)

                    axios.post('/upload_csv', {'parse_csv':vm.requestCSv}).then(response => {
                        console.log(response);
                        vm.parse_csv = response.data
                        swal("You Import successful ", "you data have been save and send to anyone", "success");
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
