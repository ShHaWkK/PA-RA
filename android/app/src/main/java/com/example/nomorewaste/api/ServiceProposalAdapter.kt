package com.example.nomorewaste

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ServiceProposal

class ServiceProposalAdapter(
    private val proposals: List<ServiceProposal>
) : RecyclerView.Adapter<ServiceProposalAdapter.ServiceProposalViewHolder>() {

    class ServiceProposalViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val proposalName: TextView = view.findViewById(R.id.proposal_name)
        val proposalDescription: TextView = view.findViewById(R.id.proposal_description)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ServiceProposalViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_proposal, parent, false)
        return ServiceProposalViewHolder(view)
    }

    override fun onBindViewHolder(holder: ServiceProposalViewHolder, position: Int) {
        val proposal = proposals[position]
        holder.proposalName.text = proposal.name
        holder.proposalDescription.text = proposal.description
    }

    override fun getItemCount(): Int = proposals.size
}
