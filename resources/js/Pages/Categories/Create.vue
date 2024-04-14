<template>
  <div>
    <Head :title="title" />
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="store">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full " label="Name" />
<!--          <text-input v-model="form.color" :error="form.errors.color" class="pr-6 pb-8 w-full lg:w-1/2 color-picker" label="Color" />-->
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Category</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'
import { Link, Head } from '@inertiajs/vue3'

export default {
  metaInfo: { title: 'Create Category' },
  components: {
    LoadingButton,
    TextInput,
    Link,
    Head,
  },
  layout: Layout,
  props: {
      title: String
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        color: null
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('categories.store'))
    },
  },
}
</script>
