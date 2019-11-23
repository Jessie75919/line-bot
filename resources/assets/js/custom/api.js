const headers = (contentType = 'application/json') => {
  return {
    'Content-Type': contentType,
    'X-Requested-With': 'XMLHttpRequest',
  };
};

export const get = (url, params = {}) =>
  axios({
    method: 'get',
    params,
    url,
    headers: headers(),
  }).then(res => res)
    .catch(err => alert(err));

export const download = url =>
  axios({
    method: 'get',
    url,
    headers: headers(),
    responseType: 'blob', // important
  }).then(res => res)
    .catch(err => alert(err));

export const post = (url, data) =>
  axios({
    method: 'post',
    url,
    data,
    headers: headers(),
  }).then(res => res)
    .catch(err => alert(err));

export const patch = (url, data) =>
  axios({
    method: 'patch',
    url,
    data,
    headers: headers(),
  }).then(res => res)
    .catch(err => alert(err));

export const remove = url =>
  axios({
    method: 'delete',
    url,
    headers: headers(),
  }).then(res => res)
    .catch(err => alert(err));