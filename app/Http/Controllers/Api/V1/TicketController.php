<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\v1\TicketPolicy;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Get all tickets
     *
     * @group Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a ticket
     *
     * Created a new ticket. Users can only create tickets for themeselves. Managers can create tickets for any user.
     *
     * @group Managing Tickets
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Show a specific ticket
     *
     * Display an individual ticket.
     *
     * @group Managing Tickets
     *
     */
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }

        return new TicketResource($ticket);
    }

    /**
     * Update Ticket
     *
     * Update the specified ticket in storage.
     *
     * @group Managing Tickets
     *
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Replace Ticket
     *
     * Replace the specified ticket in storage.
     *
     * @group Managing Tickets
     *
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Delete ticket
     *
     * Remove the specified resource from storage.
     *
     * @group Managing Tickets
     *
     */
    public function destroy(Ticket $ticket)
    {
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        }

        return $this->notAuthorized('You are not authorized to delete that resource');
    }
}
