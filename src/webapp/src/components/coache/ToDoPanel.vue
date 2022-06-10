<template>
  <div class="todo-panel" :class="{ 'active': active }">
    <button @click="togglePanel" class="btn btn-white-primary todo-panel--trigger">
      <img v-if="active" src="@/assets/img/arrow-right.svg" />
      <img v-else src="@/assets/img/to-do.svg" />
    </button>
    <div class="todo-panel--wrap">
      <button class="btn todo-sm-close d-inline-block d-md-none" @click="togglePanel">
        <img src="@/assets/img/croix.svg" alt="">
      </button>
      <h1 class="font-weight-bold font-size-21 text-uppercase">To Do List</h1>
      <div class="todo-panel--list mt-3">
        <div class="todo-panel--item" v-for="(item, index) in todos" :key="index">
          <div class="form-group radio mb-0 text-black border-bottom border-black">
            <div class="radio-items">
              <div class="radio-item">
                <input type="checkbox" :id="`todo-item-${item.id}`" :value="true" v-model="item.done" :disabled="item.done" @click="toggleTodoStatus(item)" />
                <label class="font-size-12" :class="{'disabled': item.done}" :for="`todo-item-${item.id}`">{{ item.label }}</label>
              </div>
            </div>
          </div>
        </div>

        <div class="todo-panel--create-item mt-5 form-group">
          <button class="create-btn" @click="enableTodo" v-if="!formEnabled">
            <img src="@/assets/img/plus-primary.svg" height="24px" width="24px" alt />
          </button>
          <form v-if="formEnabled" @submit="createTodo">
            <input v-if="formEnabled"
                   ref="todoText"
                   type="text"
                   placeholder="Entrez le texte de la tâche"
                   v-model="text"
                   class="form-control"
                   @blur="formEnabled = false" />
          </form>
        </div>
      </div>
    </div>
    <div class="todo-panel--bd" @click="togglePanel"></div>
  </div>
</template>
<script>
  import { CREATE_TODO_ITEM } from "@/graphql/todo/create-todo-item-mutation";
  import { MARK_TODO_AS_DONE } from "@/graphql/todo/mark-todo-as-done-mutation";
  import izitoast from "izitoast";
  import {CANDIDATE_BY_ID} from "@/graphql/user/candidate-by-id-query";

  export default {
    name: "TodoPanel",
    props: {
      active: {
        type: Boolean,
        default: false
      },
      program: {
        type: Object,
        required: true,
      },
      candidate: {
        type: Object,
        required: true,
      },
    },
    methods: {
      togglePanel() {
        this.$emit("toggle");
      },
      createTodo() {
        if (!this.text) {
          izitoast.error({
            position: "topRight",
            title: "Erreur",
            message: "Le texte ne peut pas être vide",
          });
          return;
        }

        this.formEnabled = false;
        let candidateId = this.candidate.user.id,
            programId = this.program.id;

        this.$apollo.mutate({
          mutation: CREATE_TODO_ITEM,
          variables: {
            label: this.text,
            programId: this.program.id,
            userId: this.candidate.user.id,
          },
          update (cache, { data }) {
            let { candidateById } = cache.readQuery({
              query: CANDIDATE_BY_ID,
              variables: {id: candidateId, programId: programId}
            });
            candidateById.program.todos.count++;
            candidateById.program.todos.items.push(data.createTodoItem);
            cache.writeQuery({
              query: CANDIDATE_BY_ID,
              variables: {id: candidateId, programId: programId},
              data: {
                candidateById
              }
            });
          }
        }).then(response => {
          this.text = '';
          this.$emit('create', response.data.createTodoItem);
          this.todos.push(response.data.createTodoItem);
        });
      },
      toggleTodoStatus(todo) {
        todo.done = true;
        this.$emit('done', todo);
        let candidateId = this.candidate.user.id,
            programId = this.program.id;

        this.$apollo.mutate({
          mutation: MARK_TODO_AS_DONE,
          variables: {
            todoId: todo.id,
          },
          update (cache, { data }) {
            let { candidateById } = cache.readQuery({
              query: CANDIDATE_BY_ID,
              variables: {id: candidateId, programId: programId}
            });
            candidateById.program.todos.count--;
            candidateById.program.todos.items = candidateById.program.todos.items.filter(item => item.id !== data.markTodoAsDone.id);
            cache.writeQuery({
              query: CANDIDATE_BY_ID,
              variables: {id: candidateId, programId: programId},
              data: {
                candidateById
              }
            });
          }
        }).then(response => {
          this.$emit('remove', todo);
          this.todos = this.todos.filter(item => item.id !== todo.id);
        });
      },
      enableTodo() {
        this.formEnabled = true;
        this.$nextTick(() => {
          this.$refs.todoText.focus();
        });
      }
    },
    data() {
      return {
        loading: false,
        formEnabled: false,
        text: '',
        todos: [],
      };
    },
    mounted() {
      this.todos = [...this.program.todos.items];
    }
  };
</script>