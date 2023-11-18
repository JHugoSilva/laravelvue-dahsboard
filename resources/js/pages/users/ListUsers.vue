<script setup>
import { onMounted, reactive, ref } from "vue";
import { Form, Field } from 'vee-validate'
import * as yup from 'yup'

const users = ref([]);

const schema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  password: yup.string().required().min(8)
})

const getUser = () => {
  axios.get("/api/users").then((response) => {
    users.value = response.data;
  });
};

const createUser = (values, { resetForm }) => {
    axios.post('/api/users', values).then((response) => {
      users.value.unshift(response.data)
      $('#createUserModal').modal('hide')
      resetForm()
    })
}

onMounted(() => {
  getUser();
});
</script>


<template>
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Users</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Users</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <!-- Button trigger modal -->
      <button
        type="button"
        class="btn btn-primary mb-2"
        data-toggle="modal"
        data-target="#createUserModal"
      >
        Add New User
      </button>
      <div class="card">
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered Date</th>
                <th>Role</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="createUserModal"
      data-backdrop="static"
      tabindex="-1"
      role="dialog"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Add New User</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <Form @submit="createUser" :validation-schema="schema" v-slot="{ errors }">
              <div class="form-group">
                <label for="name">Name</label>
                <Field
                  type="text"
                  name="name"
                  :class="{'is-invalid':errors.name}"
                  class="form-control"
                  id="name"
                  aria-describedby="nameHelp"
                  placeholder="Enter full name"
                />
                <span class="error-valid">{{ errors.name }}</span>
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <Field
                  type="email"
                  name="email"
                  :class="{'is-invalid':errors.email}"
                  class="form-control"
                  id="email"
                  aria-describedby="nameHelp"
                  placeholder="Enter full name"
                />
                <span class="error-valid">{{ errors.email }}</span>
              </div>

              <div class="form-group">
                <label for="email">Password</label>
                <Field
                  type="password"
                  name="password"
                  :class="{'is-invalid':errors.password}"
                  class="form-control"
                  id="password"
                  aria-describedby="nameHelp"
                  placeholder="Enter password"
                />
                <span class="error-valid">{{ errors.password }}</span>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                >
                  Save
                </button>
              </div>
            </Form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content -->
</template>
<style>
.error-valid {
  color:#DC3545;
}
</style>