/// <reference path="../types/generated.d.ts" />

export type CommentInput = Nvl.Comments.Data.Mutations.CreateCommentData;
export type ArticleCommentsProps = {
    articleId: string | number;
    summaries: Nvl.Comments.Data.OwnerCommentSummaries;
    createUrl: string; // Host-provided named route URL, never guessed by this module.
};
export type PackageFailure = {
    message: string;
    code: string;
    context: Record<string, unknown>;
};
export function isPackageFailure(value: unknown): value is PackageFailure {
    if (typeof value !== 'object' || value === null) return false;
    const candidate = value as Record<string, unknown>;
    return typeof candidate.message === 'string' && typeof candidate.code === 'string'
        && typeof candidate.context === 'object' && candidate.context !== null && !Array.isArray(candidate.context);
}
export async function submitComment(url: string, input: CommentInput, csrfToken: string): Promise<unknown> {
    const response = await fetch(url, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(input),
    });
    const payload: unknown = await response.json();
    if (!response.ok) {
        if (isPackageFailure(payload)) throw payload;
        throw new Error('The request failed. Handle Laravel validation separately.');
    }
    return payload; // Validate the selected host response shape before rendering it.
}
