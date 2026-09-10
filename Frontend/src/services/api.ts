export async function api<T>(url: string, options: RequestInit = {}): Promise<T> {
  const response = await fetch(`/api${url}`, {
    ...options,
    headers: {
      Accept: 'application/json',
      ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      ...options.headers,
    },
  })

  const body = await response.json().catch(() => null)

  if (!response.ok) {
    const message = body?.errors
      ? (Object.values(body.errors) as string[][]).flat().join(' ')
      : body?.message || 'Não foi possível concluir a operação.'

    throw new Error(message as string)
  }

  return body as T
}
