<style>
.wrapper--setup .setup-page {
  margin-top: 150px !important;
}
.setup-page-logo, .auth-page-logo {
  width: 180px;
  height: 140px;
}
.wrapper--setup .bg-image .setup-page-logo {
  margin-top: 4px;
}
.panel.panel-default.captcha-right {
  float: right;
}
.done-check i {
  color: #b2c85f;
}
</style>
<template>
  <setup-wrap :additional-classes="{'invitation-wrap': true}">
    <perfect-scrollbar>
      <div class="col-sm-11 col-md-8 col-lg-4 mx-auto mt-10">
        <p class="text-gris_44n inscription-paragraph" v-html="headerText" />
      </div>
      <div class="container loading-container" :class="{'loading': loading, 'done': isDone}">
        <div class="col-md-8 col-sm-12 mx-auto">
          <form-wizard @on-complete="submit" color="#7F267B" title="" subtitle="">
            <tab-content title="Informations personnelles" icon="fa fa-user" :before-change="validatePersonalInformation">
              <form>
                <fieldset>
                  <legend>Informations personnelles</legend>
                  <div class="panel panel-default">
                    <div class="panel-body">
                      <div class="form-row">
                        <div class="col-md-5 form-group radio">
                          <label>Civilité*</label>
                          <div class="radio-items mt-2">
                            <div class="radio-item">
                              <input class="form-check-input" name="civility" v-validate="'required'" data-vv-scope="personal" type="radio" value="m" v-model="user.civility" id="civility_m" />
                              <label class="form-check-label" for="civility_m">M.</label>
                            </div>
                            <div class="radio-item ml-1">
                              <input class="form-check-input" name="civility" v-validate="'required'" data-vv-scope="personal" type="radio" value="mme" v-model="user.civility" id="civility_mme" />
                              <label class="form-check-label" for="civility_mme">Mme</label>
                            </div>
                          </div>
                          <div v-if="errors.has('personal.civility')" class="invalid-feedback">
                            <p>Le champ Civilité est obligatoire</p>
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Date de naissance*</label>
                          <el-date-picker
                                  v-model="user.birthDate"
                                  format="dd/MM/yyyy"
                                  placeholder="Date de naissance"
                                  name="birthDate"
                                  data-vv-scope="personal"
                                  v-validate="'required'"
                                  :picker-options="{'firstDayOfWeek': 1}"
                                  :calendar-button="true"
                                  :required="true"
                                  :bootstrap-styling="true" />
                          <div v-if="errors.has('personal.birthDate')" class="invalid-feedback">
                            <p>Le champ Date de naissance est obligatoire</p>
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Prénom*</label>
                          <input type="text"
                                class="form-control"
                                :class="{'is-invalid': errors.has('personal.firstName')}"
                                id="firstName"
                                name="firstName"
                                v-model="user.firstName"
                                data-vv-scope="personal"
                                placeholder="Prénom ..."
                                v-validate="'required'" />
                          <div v-if="errors.has('personal.firstName')" class="invalid-feedback">
                            <p>Le champ Prénom est obligatoire</p>
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Nom*</label>
                          <input type="text"
                                class="form-control"
                                :class="{'is-invalid': errors.has('personal.lastName')}"
                                id="lastName"
                                name="lastName"
                                v-model="user.lastName"
                                data-vv-scope="personal"
                                placeholder="Nom ..."
                                v-validate="'required'" />
                          <div v-if="errors.has('personal.lastName')" class="invalid-feedback">
                            <p>Le champ Nom est obligatoire</p>
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Email*</label>
                          <input type="email"
                                class="form-control"
                                :class="{'is-invalid': errors.has('personal.email')}"
                                id="email"
                                name="email"
                                v-model="user.email"
                                data-vv-scope="personal"
                                placeholder="Email ..."
                                v-validate="'required|email'" />
                          <div v-if="errors.has('personal.email') && errors.firstByRule('personal.email', 'email')" class="invalid-feedback">
                            <p>Veuillez saisir une adresse email valide</p>
                          </div>
                          <div v-else-if="errors.has('email')" class="invalid-feedback">
                            <p>L'email existe déjà</p>
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Téléphone*</label>
                          <input  :class="{ 'is-invalid': errors.has('personal.phone') }"
                                  class="form-control"
                                  id="phone"
                                  name="phone"
                                  placeholder="01 05 33 16 60..."
                                  v-model="user.phone"
                                  data-vv-scope="personal"
                                  v-validate="{required: true, regex: '^(?:(?:\\+|00)33|0)\\s*[1-9](?:[\\s.-]*\\d{2}){4}$' }" />
                          <div v-if="errors.has('personal.phone') && errors.firstByRule('personal.phone', 'required')" class="invalid-feedback">
                            <p>Le champ Téléphone est obligatoire</p>
                          </div>
                          <div v-else-if="errors.has('personal.phone')" class="invalid-feedback">
                            <p>Le numéro de téléphone est dans un format invalide</p>
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-7">
                          <div class="form-group">
                            <label>Adresse</label>
                            <input class="form-control" placeholder="1 rue de test, 75008 Paris" v-model="user.address" />
                          </div>
                        </div>
                        <div class="col-md-3 offset-md-1">
                          <div class="form-group">
                            <label>Code postal*</label>
                            <div class="row">
                              <div class="col-md-12">
                                <villes-france-autocomplete
                                    :initial-query="user.userCodePostal"
                                    v-model="user.userCodePostal"
                                    name="userCodePostal"
                                    data-vv-scope="personal"
                                    v-validate="'required'"
                                    @item-selected="updateUserPostCode"
                                    :empty-query-after-selection="false"
                                />
                              </div>
                            </div>
                            <div v-if="errors.has('personal.userCodePostal')" class="invalid-feedback">
                              <p>Le champ Code Postal est obligatoire</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5">
                          <div class="form-group">
                            <label>Ville</label>
                            <input
                                class="form-control"
                                id="userCity"
                                name="userCity"
                                type="text"
                                placeholder="Ville"
                                v-model="user.userCity" />
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1">
                          <div class="form-group">
                            <label>Département</label>
                            <input
                                v-model="user.userDepartment"
                                id="userDepartment"
                                name="userDepartment"
                                type="text"
                                class="form-control"
                                placeholder="Département" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </form>
            </tab-content>
            <tab-content title="Informations professionnelles" icon="fa fa-business-time" :before-change="validateProfessionalInformation">
              <form>
                <fieldset>
                  <legend>Informations professionnelles</legend>
                  <div class="panel panel-default">
                    <div class="panel-body">
                      <div class="form-row">
                        <div class="col-md-5">
                          <div class="form-group select">
                            <label>Catégorie professionnelle*</label>
                            <select v-model="user.professionalCategory" name="professionalCategory" class="form-control" data-vv-scope="professional" v-validate="'required'">
                              <option value disabled>Sélectionner une Catégorie</option>
                              <option v-for="category in professionalCategories" :value="category.id" :key="category.id">
                                {{ category.label }}
                              </option>
                            </select>
                            <div v-if="errors.has('professional.professionalCategory')" class="invalid-feedback">
                              <p>Le champ Catégorie professionnelle est obligatoire</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1">
                          <div class="form-group select">
                            <label class>Fonction*</label>
                            <select v-model="user.function" name="function" class="form-control" data-vv-scope="professional" v-validate="'required'">
                              <option value disabled>Sélectionner une Fonction</option>
                              <option v-for="func in company.functions" :value="func" :key="func">
                                {{ func }}
                              </option>
                            </select>
                            <div v-if="errors.has('professional.function')" class="invalid-feedback">
                              <p>Le champ Fonction est obligatoire</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5">
                          <div class="form-group">
                            <label>Date d'ancienneté dans l'entreprise</label>
                            <el-date-picker
                                    v-model="user.seniorityDate"
                                    name="seniorityDate"
                                    format="dd/MM/yyyy"
                                    placeholder="Date d'ancienneté dans l'entreprise"
                                    :picker-options="{'firstDayOfWeek': 1}"
                                    :calendar-button="true"
                                    :required="true"
                                    :bootstrap-styling="true" />
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1">
                          <div class="form-group">
                            <label>Ancienneté dans la fonction</label>
                            <input class="form-control"
                                  type="text"
                                  name="previousFunction"
                                  placeholder="En nombre d'années"
                                  v-model="user.previousFunction" />
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5">
                          <div class="form-group select">
                            <label>Service/Département*</label>
                            <select v-model="user.service" name="service" class="form-control" data-vv-scope="professional" v-validate="'required'">
                              <option value disabled>Sélectionner un service</option>
                              <option v-for="service in company.services" :value="service" :key="service">
                                {{ service }}
                              </option>
                            </select>
                            <div v-if="errors.has('professional.service')" class="invalid-feedback">
                              <p>Le champ Service est obligatoire</p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1">
                          <div class="form-group">
                            <label>Rémunération brute</label>
                            <input  v-model="user.annualCompensation"
                                    id="annualCompensation"
                                    name="annualCompensation"
                                    class="form-control"
                                    type="text"
                                    placeholder="Rémunération" />
                          </div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5">
                          <div class="form-group">
                            <label>LinkedIn</label>
                            <input class="form-control"
                                    type="text"
                                    placeholder="https://www.linkedin.com/company/oasys-consultants/"
                                    v-model="user.linkedin" />
                          </div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group radio">
                          <label>Modalité de travail*</label>
                          <div class="radio-items">
                            <div class="radio-item">
                              <input class="form-check-input" name="workMode" type="radio" value="onsite" v-validate="'required'" data-vv-scope="professional" v-model="user.workMode" id="workMode_onsite" />
                              <label class="form-check-label" for="workMode_onsite">sur site</label>
                            </div>
                            <div class="radio-item">
                              <input class="form-check-input" name="workMode" type="radio" value="distance" v-validate="'required'" data-vv-scope="professional" v-model="user.workMode" id="workMode_distance" />
                              <label class="form-check-label" for="workMode_distance">à distance</label>
                            </div>
                          </div>
                          <div v-if="errors.has('professional.workMode')" class="invalid-feedback">
                            <p>Le champ Modalité de travail est obligatoire</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </form>
            </tab-content>
            <tab-content title="Confirmation" icon="fa fa-check">
              <form>
                <fieldset v-show="!isDone">
                  <legend>Informations personnelles</legend>
                  <div class="panel panel-default">
                    <div class="panel-body">
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Civilité</label>
                          <div>{{ user.civility === 'm' ? 'M.' : 'Mme.' }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Date de naissance</label>
                          <div>{{ user.birthDate ? dateFormatter(user.birthDate) : '' }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Prénom</label>
                          <div>{{ user.firstName }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Nom</label>
                          <div>{{ user.lastName }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Email</label>
                          <div>{{ user.email }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Téléphone</label>
                          <div>{{ user.phone }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Adresse</label>
                          <div>{{ user.address }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Code postal</label>
                          <div>{{ user.userCodePostal }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Ville</label>
                          <div>{{ user.userCity }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Département</label>
                          <div>{{ user.userDepartment }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
                <fieldset v-show="!isDone">
                  <legend>Informations Professionnelles</legend>
                  <div class="panel panel-default">
                    <div class="panel-body">
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Catégorie professionnelle</label>
                          <div>{{ user.professionalCategory ? professionalCategories.find(p => p.id === user.professionalCategory).label : '' }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Fonction</label>
                          <div>{{ user.function }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Date d'ancienneté dans l'entreprise</label>
                          <div>{{ user.seniorityDate ? dateFormatter(user.seniorityDate) : '' }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Ancienneté dans la fonction</label>
                          <div>{{ user.previousFunction }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>Service</label>
                          <div>{{ user.service }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Rémunération brute</label>
                          <div>{{ user.annualCompensation }}</div>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="col-md-5 form-group">
                          <label>LinkedIn</label>
                          <div>{{ user.linkedin }}</div>
                        </div>
                        <div class="col-md-5 offset-md-1 form-group">
                          <label>Modalité de travail</label>
                          <div>{{ user.workMode.map(m => m === 'onsite' ? 'sur site' : 'à distance').join(', ') }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
                <fieldset>
                  <legend v-show="!isDone">Confirmation</legend>
                  <div class="panel panel-default captcha-right" v-show="!isDone">
                    <div class="panel-body">
                      <div class="form-row">
                        <div class="col-md-11">
                          <vue-recaptcha :sitekey="recaptchaSiteKey" :loadRecaptchaScript="true" @verify="verifyCaptcha" />
                          <div v-if="!captchaVerified && submitted" class="invalid-feedback">
                            <p>Vous devez vérifier CAPTCHA pour soumettre le formulaire</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel panel-default" v-show="isDone">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-4 done-check">
                          <i class="fa fa-check" />
                        </div>
                        <div class="col-md-7">
                          <p class="font-weight-bold">Merci pour ces éléments !</p>
                          <p class="font-weight-bold">Votre inscription a bien été prise en compte.</p>
                          <p class="font-weight-bold">Un e-mail vous sera envoyé dès que votre profil sera validé. Il vous permettra de créer votre mot de passe et d’accéder aux calendriers de nos consultants.</p>
                          <p class="font-italic text-muted">Pour toute question relative au fonctionnement de la plateforme ou à votre inscription, vous pouvez nous écrire à <a href="mailto:support-digital@oasys.fr">support-digital@oasys.fr</a>.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </form>
            </tab-content>
            <template v-slot:prev>
              <button class="btn btn-outline-consultant-light" type="button"><span>Précédent</span></button>
            </template>
            <template v-slot:next>
              <button class="btn btn-gradient-primary" type="button"><span>Suivant</span></button>
            </template>
            <template v-slot:finish>
              <button class="btn btn-gradient-primary" type="button"><span>Soumettre</span></button>
            </template>
          </form-wizard>
        </div>
      </div>
      <div class="loading-target"><span></span></div>
    </perfect-scrollbar>
  </setup-wrap>
</template>

<script>
import SetupWrap from "@/components/wrappers/Setup";
import { FormWizard, TabContent, WizardStep } from "vue-form-wizard";
import VillesFranceAutocomplete from "@/components/autocomplete/VillesFranceAutocomplete";
import "vue-form-wizard/dist/vue-form-wizard.min.css";
import { CREATE_USER_FROM_OFFLINE_FORM } from "@/graphql/user/create-user-from-offline-form-mutation";
import { ALL_PROFESSIONAL_CATEGORIES } from "@/graphql/user/all-professional-categories";
import { PROGRAM_PIC_BY_LINK } from "@/graphql/program/program-pic-by-link";
import { COMPANY_BY_LINK_ID } from "@/graphql/company/company-by-link-id-query";
import VueRecaptcha from "vue-recaptcha";
import izitoast from "izitoast";
import { PerfectScrollbar } from "vue2-perfect-scrollbar";
import { apolloClient } from "@/apollo";
import localMoment from "@/utils/localMoment";

export default {
  name: "UserInvite",
  components: {
    SetupWrap,
    FormWizard,
    TabContent,
    VillesFranceAutocomplete,
    VueRecaptcha,
    PerfectScrollbar,
  },
  data: () => ({
    user: {
      firstName: "",
      lastName: "",
      birthDate: "",
      civility: "",
      phone: "",
      userCity: "",
      userDepartment: "",
      userCodePostal: "",
      email: "",
      professionalCategory: "",
      previousFunction: "",
      function: "",
      service: "",
      annualCompensation: "",
      linkedin: "",
      workMode: [],
    },
    company: {
      functions: [],
      services: [],
    },
    professionalCategories: [],
    captchaVerified: false,
    submitted: false,
    loading: false,
    isDone: false,
    recaptchaToken: null,
    headerText: null,
  }),
  computed: {
    recaptchaSiteKey() {
      return process.env.VUE_APP_RECAPTCHA_SITE_KEY;
    },
  },
  mounted() {
    this.$validator.pause();
  },
  apollo: {
    professionalCategories: {
      query: ALL_PROFESSIONAL_CATEGORIES,
      update: (data) => data.allProfessionalCategories.items,
    },
    company: {
      query: COMPANY_BY_LINK_ID,
      variables() {
        return {
          linkId: this.$route.params.linkId,
        };
      },
      update: (data) => data.companyByLinkId,
    },
  },
  methods: {
    validatePersonalInformation() {
      this.$validator.resume();
      return new Promise((resolve, reject) => {
        this.$validator
          .validate("personal.*")
          .then((res) => {
            console.log(res);
            console.log(this.errors);
            if (res) {
              this.$validator.pause();
            }
            resolve(res);
          })
          .catch((res) => {
            this.$validator.pause();
            resolve(false);
          });
      });
    },
    validateProfessionalInformation() {
      this.$validator.resume();
      return this.$validator.validate("professional.*");
    },
    setUserPostCode(code) {
      this.user.userCodePostal = code;
    },
    updateUserPostCode(ville) {
      this.user.userCity = ville.commonName;
      this.user.userDepartment = ville.departmentName;
      this.user.userCodePostal = ville.codePostal;
    },
    verifyCaptcha($event) {
      this.recaptchaToken = $event;
      this.captchaVerified = true;
    },
    dateFormatter (date) {
      return localMoment(date).format('DD/MM/YYYY');
    },
    submit() {
      this.submitted = true;
      if (!this.captchaVerified) {
        return;
      }

      this.loading = true;
      this.$apollo
        .mutate({
          mutation: CREATE_USER_FROM_OFFLINE_FORM,
          variables: {
            ...this.user,
            linkId: this.$route.params.linkId,
            recaptchaToken: this.recaptchaToken,
            workMode: this.user.workMode.join(', '),
          },
        })
        .then((response) => {
          this.isDone = true;
          this.loading = false;
        })
        .catch((response) => {
          izitoast.error({
            position: "topRight",
            title: "Erreur",
            message:
              "Échec de l'envoi du formulaire, veuillez vous assurer que l'URL d'invitation est correcte!",
          });
          this.loading = false;
        });
    },
  },
  created() {
    //get text
    apolloClient
      .query({
        query: PROGRAM_PIC_BY_LINK,
        variables: { id: this.$route.params.linkId },
      })
      .then((d) => {
        this.headerText =
          d.data.programPicOfflineTextFromLinkId.inscriptionText;
      });
  },
};
</script>