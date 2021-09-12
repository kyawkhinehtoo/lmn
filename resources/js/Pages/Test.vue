<template>
  <app-layout>
       <jet-button class="ml-2" @click="displayingToken=true" >
                    Save
      </jet-button>
      <jet-button class="ml-2" @click="apiTokenBeingDeleted=true" >
                    Confirm
      </jet-button>
      <jet-dialog-modal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API Token
            </template>

            <template #content>
                <div>
                    Please copy your new API token. For your security, it won't be shown again.
                </div>

            </template>

            <template #footer>
              
                <jet-secondary-button @click="displayingToken = false">
                    Close
                </jet-secondary-button>
                <jet-danger-button @click="displayingToken = false">
                    Close
                </jet-danger-button>
            </template>
        </jet-dialog-modal>
   
  </app-layout>
</template>

<script>
import { ref, reactive, emits } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";
import JetButton from '@/Jetstream/Button'
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal'

export default {
  components: {
    AppLayout,
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetConfirmationModal,
  },
  name: "Test",
  setup(props) {
    const form = useForm({
      name: null,
      file: null,
      incident_id: props.data,
    });
    let displayingToken = ref(false);
    let apiTokenBeingDeleted = ref(null);
    function deleteApiToken(e){
      console.log(e);
    }
    return { form,displayingToken,apiTokenBeingDeleted,deleteApiToken};
  },
};
</script>
