<script setup>
import { onMounted, reactive, ref } from "vue";
import { Form, Field , useResetForm } from 'vee-validate'
import { formatDate } from '../../helper.js'
import * as yup from 'yup'
import { useToastr } from '../../toastr.js'

const users = ref([]);
const editing = ref(false)
const formValues = ref()
const form = ref(null)
const userIdBeingDeleted = ref(null)

const toastr = useToastr()

const createUserSchema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  password: yup.string().required().min(8)
})

const editUserSchema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  password: yup.string().when((password, schema) => {
    if (password[0] != undefined && password[0].length && password[0] != [undefined]) {
      return password ? schema.required().min(8) : schema;
    }
  })
})

const getUser = () => {
  axios.get("/api/users").then((response) => {
    users.value = response;
  });
};

const createUser = (values, { resetForm, setErrors }) => {
    axios.post('/api/users', values).then((response) => {
      users.value.unshift(response.data)
      $('#userFormModal').modal('hide')
      resetForm()
    }).catch((error) => {
      setErrors(error.response.data.errors)
    })
}

const updateUser = (values, { resetForm, setErrors }) => {
    axios.put('/api/users/'+formValues.value.id, values).then((response) => {
      const index = users.value.data.findIndex(user => user.id === response.data.id)
      users.value.data[index] = response.data
      $('#userFormModal').modal('hide')
      toastr.success('User updated successfully!')
      resetForm()
    }).catch((error) => {
      setErrors(error.response.data.errors)
    })
}

const handleSubmit = (values, actions) => {
  if (editing.value) {
      updateUser(values, actions)
  } else {
      createUser(values, actions)
  }
}


const editUser = (user) => {
  editing.value = true
  clearForm(form.value)
  $('#userFormModal').modal('show')
  formValues.value = {
    id: user.id,
    name: user.name,
    email: user.email
  }
}

const confirmUserDeletion = (user) => {
  userIdBeingDeleted.value = user.id
  $('#deleteUserModal').modal('show')
}

const deleteUser = () => {
  axios.delete(`/api/users/${userIdBeingDeleted.value}`)
  .then(()=>{
    $('#deleteUserModal').modal('hide')
    console.log(users.value)
    users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value)
    toastr.success('User deleted successfully')
  })
}

const addUser = () => {
  editing.value = false
  clearForm(formValues.value);
  $('#userFormModal').modal('show')
}

onMounted(() => {
  getUser();
});

const clearForm = (values) => {
  for (let props in values) {
      values[props] = ''
  }
}

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
        @click="addUser"
        class="btn btn-primary mb-2"
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
              <tr v-for="(user, index) in users.data" :key="index">
                <td>{{ ++index }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ formatDate(user.created_at) }}</td>
                <td>{{ user.role }}</td>
                <td>
                  <a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></a>
                  <a href="#" @click.prevent="confirmUserDeletion(user)"><i class="fa fa-trash text-danger ml-2"></i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="userFormModal"
      data-backdrop="static"
      tabindex="-1"
      role="dialog"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <span v-if="editing">Edit User</span>
            <span v-else>Add New User</span>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="resetForm"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors }" :initial-values="formValues">
              <div class="form-group">
                <label for="name">Name</label>
                <Field
                  type="text"
                  name="name"
                  :class="{ 'is-invalid':errors.name }"
                  class="form-control"
                  id="name"
                  aria-describedby="nameHelp"
                  placeholder="Enter full name"
                />
                <span id="errorName" class="error-valid">{{ errors.name }}</span>
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

     <div
      class="modal fade"
      id="deleteUserModal"
      data-backdrop="static"
      tabindex="-1"
      role="dialog"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h6>Delete User</h6>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="resetForm"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <h5>Are you sure you want to delete this user ?</h5>
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
                  type="button"
                  @click.prevent="deleteUser"
                  class="btn btn-primary"
                >
                  Delete User
                </button>
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