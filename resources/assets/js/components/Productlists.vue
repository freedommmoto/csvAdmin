<template lang="html">
  <div class="chat-log">
  loaddd
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
    props: ['parse_csv'],
    data() {
       
    },
    methods: {
        sortBy: function (key) {
            var vm = this
            vm.sortKey = key
            vm.sortOrders[key] = vm.sortOrders[key] * -1
        },
    }
}
</script>

<style lang="css">
</style>
