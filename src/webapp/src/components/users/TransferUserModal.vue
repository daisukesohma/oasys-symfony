<template>
  <div class="modal d-block" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Transférer vers un spécialiste</h5>
          <button type="button" class="close" @click="$emit('close')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row ">
            <div class="col-md-12">
              <div class="form-group select">
                <label>Specialité</label>

                <select class="form-control"
                        name="coachSpeciality"
                        v-model="coachSpeciality"
                        :class="{'is-invalid': coachSpecialityError}">
                  <option></option>
                  <option v-for="opt in coachSpecialityList" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                  </option>
                </select>

                <div class="d-block invalid-feedback" v-if="coachSpecialityError">
                  Le champ Spécialité est obligatoire
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-consultant-light" @click="$emit('close')">Annuler</button>
          <button type="button" class="btn btn-gradient-primary" @click="confirm">
            Confirmer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {COACH_SPECIALITY_LIST} from "@/enum/coachSpecialityEnum";

export default {
  name: "TransferUserModal",
  props: {
    value: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      coachSpeciality: '',
      coachSpecialityError: false,
    };
  },
  computed: {
    coachSpecialityList() {
      return COACH_SPECIALITY_LIST;
    },
  },
  mounted() {
    this.coachSpeciality = this.value;
  },
  methods: {
    confirm() {
      if (!this.coachSpeciality) {
        this.coachSpecialityError = true;
        return;
      }

      this.$emit('confirm', this.coachSpeciality);
    },
  },
};
</script>
