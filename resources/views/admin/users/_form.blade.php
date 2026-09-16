<div class="mb-3">
  <label class="form-label">姓名</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">Email</label>
  <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">密碼{{ isset($user) ? '（留空表示不更改）' : '' }}</label>
  <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">角色</label>
    <select name="role" class="form-select">
      <option value="member" {{ old('role', $user->role ?? 'member') === 'member' ? 'selected' : '' }}>Member</option>
      <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">狀態</label>
    <select name="status" class="form-select">
      <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>啟用</option>
      <option value="suspended" {{ old('status', $user->status ?? '') === 'suspended' ? 'selected' : '' }}>停用</option>
    </select>
  </div>
</div>

<button type="submit" class="btn btn-dark">儲存</button>
<a href="{{ route('admin.users.index') }}" class="btn btn-link text-muted">取消</a>
