<template>
  <div>
    <b-row class="justify-content-center">
      <b-col lg="4">
        <b-form-group>
          <b-form-file
            v-model="files"
            id="file-large"
            size="lg"
            multiple
          >
          </b-form-file>
        </b-form-group>
        <b-button variant="dark" size="lg" @click="upload()">Upload</b-button>
      </b-col>
    </b-row>
    <b-table
      class="mt-4"
      :fields="fields"
      :items="items"
    >
      <template v-slot:cell(size)="row">
        {{row.item.size}} bytes
      </template>
      <template v-slot:cell(url)="row">
        <b-link @click="download(row.item)">{{row.item.url}}</b-link>
      </template>
    </b-table>
  </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment'
import FileDownload from 'js-file-download'

export default {
  name: 'HelloWorld',
  data () {
    return {
      files: [],
      items: [],
      fields: [
        {
          key: 'name',
          label: 'Name'
        },
        {
          key: 'url',
          label: 'Download'
        },
        {
          key: 'size',
          label: 'Size'
        },
        {
          key: 'created_at',
          label: 'Uploaded'
        }
      ]
    }
  },
  mounted () {
    this.getFilesList()
  },
  methods: {
    upload () {
      const config = {
        'content-type': 'multipart/form-data'
      }

      const data = new FormData()
      for (let i = 0; i < this.files.length; i++) {
        data.append('files[]', this.files[i])
      }

      axios.post('/upload', data, config)
        .then((res) => {
          this.getFilesList()
        })
    },
    getFilesList () {
      axios.get('/filesList')
        .then((res) => {
          this.items = res.data
          this.items.forEach(function (element) {
            element.created_at = moment(element.created_at).format('DD.MM.YYYY HH:mm')
          })
        })
    },
    download (item) {
      axios({
        url: '/download/' + item.id,
        method: 'GET',
        responseType: 'blob'
      }).then((res) => {
        FileDownload(res.data, item.name)
      })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
