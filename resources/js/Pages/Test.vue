<template>
<div>
   <div class="col-span-6 sm:col-span-4 w-half">
                    <jet-label for="name" value="Name" />
                    <jet-input id="name" v-model="form.name" type="text" class="mt-1 block w-full"  autofocus />
                    <jet-input-error  class="mt-2" />
  </div>
  
</div>
</template>

<script>
import { ref,  reactive,emits  } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import JetInput from '@/Jetstream/Input';
import JetLabel from '@/Jetstream/Label';
import JetInputError from '@/Jetstream/InputError';

 export default {
  components: {
    JetInput,
    JetLabel,
    JetInputError
  },
  name: "Test",
  setup(props) {
  
    const form = useForm({
      name: null,
      file: null,
      incident_id: props.data,
    });

    return { form };
  },
};
</script>
