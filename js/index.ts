declare global {
  interface Window {
    hello: any,
  }
}

import {DefaultApi, FetchParams, Middleware, RequestContext} from "../openapi/generated/typescript-fetch";

const m1: Middleware = {
  pre(context: RequestContext): Promise<FetchParams | void> {
    console.log('hello')
    return Promise.reject()
  },
}

async function main() {
  console.log('main')
  let api = new DefaultApi()
  api = api.withMiddleware(m1)
  const res = await api.usersGet();
  console.log(res)
}

window.hello = main
